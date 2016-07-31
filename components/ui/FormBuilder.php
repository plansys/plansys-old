<?php

class FormBuilder extends CComponent {

    private $ast;

    private function getSingleFieldAST($field, $parser) {
        $keys = [];
        $int  = 0;
        foreach ($field as $k => $value) {
            $stmt = [new PhpParser\Node\Scalar\String_("")];
            if (!is_array($value)) {
                $value = str_replace("\"", "\\\"", $value);
                $value = str_replace('$', '\$', $value);
                $value = str_replace('\\$', '\$', $value);
                $stmt  = $parser->parse("<?php \"" . $value . "\";");
            } else {
                $items = $this->getSingleFieldAST($value, $parser);
                $items = $items->value->items;
                $stmt  = [new PhpParser\Node\Expr\Array_($items)];
            }

            if (empty($stmt)) {
                $stmt = [new PhpParser\Node\Scalar\Expr\ConstFetch(new PhpParser\Node\Scalar\Expr\Null)];
            }

            if ($k === $int) {
                $keys[] = new PhpParser\Node\Expr\ArrayItem(
                        $stmt[0]
                );
            } else {
                $keys[] = new PhpParser\Node\Expr\ArrayItem(
                        $stmt[0], new PhpParser\Node\Scalar\String_($k)
                );
            }

            $int++;
        }

        $ret = new PhpParser\Node\Expr\ArrayItem(
                new PhpParser\Node\Expr\Array_($keys)
        );

        return $ret;
    }

    private function getFieldAST($fields, $parser) {
        $ret = [];
        foreach ($fields as $key => $field) {
            $ret[] = $this->getSingleFieldAST($field, $parser);
        }
        return $ret;
    }

    /**
     * Expand field definition
     * 
     * @param array $fields
     * @return array
     */
    public static function expandFields($fields) {
        $processed = [];
        if (!is_array($fields))
            return $processed;

        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                $f             = self::stripSlashesRecursive($f);
                $processed[$k] = array_merge($field->attributes, $f);

                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        if (!isset($fields[$k][$i]))
                            continue;

                        $processed[$k][$i] = self::expandFields($fields[$k][$i]);
                    }
                }
            } else {
                $value         = $f;
                $processed[$k] = [
                    'type'  => 'Text',
                    'value' => str_replace("\'", "'", $value)
                ];
            }
        }
        return $processed;
    }

    private static function stripSlashesRecursive($array) {

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = stripslashes($value);
            }
            if (is_array($value)) {
                $array[$key] = self::stripSlashesRecursive($value);
            }
        }
        return $array;
    }

}
