<?php

abstract class CodeGenerator extends CComponent {

    public $class;
    protected $baseClass = "CComponent";
    protected $basePath = "application.components";
    protected $baseDir = '';
    protected $classPath;
    protected $filePath;
    protected $file;
    protected $methods = [];

    public function load($class) {
        if (!is_string($class)) {
            throw new Exception("\$class parameter should be string");
        }

        $this->class = $class;
        $this->classPath = $this->basePath . "." . $class;
        $this->filePath = Yii::getPathOfAlias($this->classPath) . ".php";
        $this->baseDir = Yii::getPathOfAlias($this->basePath);

        ## create directory
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }

        ## create file
        if (!is_file($this->filePath) || trim(file_get_contents($this->filePath)) == "") {
            file_put_contents($this->filePath, "<?php\n
class {$class} extends {$this->baseClass} {\n \n}
");

            Yii::import($this->classPath);
            if (!class_exists($this->class)) {
                eval("class {$class} extends {$this->baseClass} {\n \n}");
            }
        }


        ## get method line and length
        if (!$this->checkSession()) {
            Yii::import($this->classPath);

            $this->file = file($this->filePath, FILE_IGNORE_NEW_LINES);
            $reflector = new ReflectionClass($this->class);
            $methods = $reflector->getMethods();

            foreach ($methods as $m) {
                if ($m->class == $this->class) {
                    $line = $m->getStartLine() - 1;
                    $length = $m->getEndLine() - $line;
                    $this->methods[$m->name] = [
                        'line'   => $line,
                        'length' => $length
                    ];
                }
            }

            $this->updateSession();
        } else {
            $this->loadSession();
        }

        $this->save();
        return $this;
    }

    protected function updateSession($timestamp = null) {
        Yii::app()->session['CODEGEN_' . $this->classPath] = [
            'methods'   => $this->methods,
            'file'      => $this->file,
            'timestamp' => is_null($timestamp) ? filemtime($this->filePath) : $timestamp
        ];
    }

    protected function checkSession() {
        $session = Yii::app()->session['CODEGEN_' . $this->classPath];
        if (is_null($session)) {
            return false;
        }

        if (@$session['timestamp'] != filemtime($this->filePath) && @$session['timestamp'] != 'JUSTWRITTEN') {
            return false;
        }
        return true;
    }

    protected function loadSession() {
        $session = Yii::app()->session['CODEGEN_' . $this->classPath];
        $this->methods = $session['methods'];
        $this->file = $session['file'];
        $session['timestamp'] = filemtime($this->filePath);

        return $session;
    }

    protected function prepareLineForProperty() {

        ## get first line of the class
        $reflector = new ReflectionClass($this->class);
        $line = $reflector->getStartLine();

        ## when last line is like "{}" then separate it to new line
        $lastline = trim($this->file[count($this->file) - 1]);

        if (substr($lastline, 0, 5) == "class" && substr($lastline, -1) == "}") {
            $lastline[strlen($lastline) - 1] = " ";
            $this->file[$line - 1] = $lastline;
            $this->file[] = "";
            $this->file[] = "}";
        }

        if (substr($lastline, -1) == "}" && substr(trim($this->file[count($this->file) - 2]), -1) == "{") {
            array_splice($this->file, count($this->file) - 1, 0, '');

            foreach ($this->methods as $k => $m) {
                if ($m['line'] >= count($this->file) - 1) {
                    $this->methods[$k]['line'] += 1;
                }
            }
        }
        return $line;
    }

    public static function varExport($var, $indent = "        ") {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                            . ($indexed ? "" : CodeGenerator::varExport($key) . " => ")
                            . CodeGenerator::varExport($value, "$indent    ");
                }
                $fields = implode(",\n", $r);

                if ($fields == '') {
                    return '[]';
                } else {
                    return "[\n" . $fields . "\n" . $indent . "]";
                }
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

    public static function formatCode($fields, $indent = "        ") {
        ## get fields
        $fields = var_export($fields, true);

        ## strip numerical array keys
        $fields = preg_replace("/[0-9]+\s*\=\> /i", '', $fields);

        ## replace unwanted formatting
        $replace = [
            "  "    => '    ',
            "=> \n" => "=>"
        ];
        $fields = str_replace(array_keys($replace), $replace, $fields);
        $replace = [
            "=>        [" => '=> [',
        ];
        $fields = str_replace(array_keys($replace), $replace, $fields);
        $fields = explode("\n", $fields);

        ## indent each line
        $count = count($fields);
        foreach ($fields as $k => $f) {
            if ($k == 0)
                continue;

            $fields[$k] = $indent . $f;
            if ($k > 0 && $k < $count - 1) {
                if (trim($f) == "") {
                    unset($fields[$k]);
                }
            }
        }
        $fields = implode("\n", $fields);

        ## remove unwanted formattings
        $fields = preg_replace('/\[[\n\r\s]*\\],/', '[],', $fields);
        $fields = preg_replace('/=>\s*[/', '=> [', $fields);

        return $fields;
    }

    protected function prepareLineForMethod() {
        $first_line = $this->prepareLineForProperty();

        foreach ($this->file as $line => $content) {
            if (preg_match('/\s*(private|protected|public)\s+function\s+.*/x', $content)) {
                break;
            }
        }

        ## prepare the line
        array_splice($this->file, $line, 0, "");

        ## adjust methods line and number
        foreach ($this->methods as $k => $m) {
            if ($m['line'] >= $line) {
                $this->methods[$k]['line'] += 1;
            }
        }

        $this->updateSession();

        return $line;
    }

    protected function addProperty($property, $visibility = "public", $comment = "") {

        ## compose comment
        $topcomment = "";
        $inlinecomment = "";
        if (substr($comment, 0, 2) == "/*" || substr($comment, 0, 2) == "##") {
            $topcomment = "{$comment}\n    ";
        } else if (substr($comment, 0, 2) == "//") {
            $inlinecomment = "{$comment}";
        }

        $propertyDeclaration = "\n    {$topcomment}{$visibility} \${$property}; {$inlinecomment}";
        $propertyExist = false;

        foreach ($this->file as $line => $content) {
            if (preg_match('/\s*(private|protected|public|var)\s+\$' . $property . '\s*;/x', $content)) {
                $this->file[$line] = $propertyDeclaration;
                $propertyExist = true;
            }
        }

        $lineAdded = 0;
        if (!$propertyExist) {
            $line = $this->prepareLineForProperty();

            $properties = explode("\n", $propertyDeclaration);
            array_splice($this->file, $line, 0, $properties);
            $lineAdded = count($properties);

            ## remove blank line after property
            $nextLineIsEmpty = trim($this->file[$line + 2]) == '';
            $nextTwoLineIsEmpty = trim($this->file[$line + 3]) == '';
            $nextTwoLineIsProperty = (preg_match('/\s*(private|protected|public|var)\s+\$/x', $this->file[$line + 3]));

            if ($nextLineIsEmpty && $nextTwoLineIsProperty) {
                unset($this->file[$line + 2]);
                $lineAdded--;
                $this->file = array_values($this->file);
            }

            if ($topcomment != "" && $nextTwoLineIsEmpty) {
                unset($this->file[$line + 3]);
                $lineAdded--;
                $this->file = array_values($this->file);
            }
        }

        ## adjust methods line and number
        foreach ($this->methods as $k => $m) {
            if ($m['line'] >= $line) {
                $this->methods[$k]['line'] += $lineAdded;
            }
        }

        $this->save();
    }

    protected function removeProperty($property) {
        Yii::import($this->classPath);
        foreach ($this->file as $line => $content) {
            if (preg_match('/\s*(private|protected|public|var)\s+\$' . $property . '\s*;/x', $content)) {

                unset($this->file[$line]);
                $this->file = array_values($this->file);
                $removedLine = 1;

                ## remove comment
                if (substr(trim($this->file[$line - 1]), 0, 2) == "/*" ||
                        substr(trim($this->file[$line - 1]), 0, 2) == "##") {
                    unset($this->file[$line - 1]);
                    $this->file = array_values($this->file);
                    $removedLine++;
                }

                ## when class has no property, then remove all blank lines
                while (trim($this->file[$line]) == '') {
                    unset($this->file[$line]);
                    $this->file = array_values($this->file);
                    $removedLine++;
                }

                ## adjust methods line and number
                foreach ($this->methods as $k => $m) {
                    if ($m['line'] >= $line) {
                        $this->methods[$k]['line'] -= $removedLine;
                    }
                }
            }
        }
        $this->save();
    }

    protected function getFunctionBody($name) {
        if (isset($this->methods[$name])) {
            return array_slice($this->file, $this->methods[$name]['line'], $this->methods[$name]['length']);
        } else {
            return [];
        }
    }

    public function renameFunction($oldName, $newName, $param = null) {
        if (isset($this->methods[$oldName])) {
            ## check param
            if (!is_null($param)) {
                $body = $this->getFunctionBody($oldName);
                array_pop($body);
                array_shift($body);
                $body = implode("\n", $body);
                $this->updateFunction($oldName, $body, ['params' => $param]);
            }

            ## rename fungsinya
            $line = $this->methods[$oldName]['line'];
            $func = $this->file[$line]; // public function actionCreate($a) {
            $pos = strpos($func, $oldName);
            $len = strlen($oldName);
            $new = substr_replace($func, $newName, $pos, $len);
            $this->file[$line] = $new;

            ## rename oldname dalam daftar method
            $newMethod = [];
            foreach ($this->methods as $k => $m) {
                if ($k == $oldName)
                    $k = $newName;
                $newMethod[$k] = $m;
            }
            $this->methods = $newMethod;

            $this->updateSession();
            $this->save();
        }
    }

    protected function updateFunction($name, $body, $options = []) {

        $isNewFunc = false;
        ## get first line of the class       
        if (!isset($this->methods[$name])) {
            $line = $this->prepareLineForMethod();
            $length = 0;
            $isNewFunc = true;
        } else {
            $line = $this->methods[$name]['line'];
            $length = $this->methods[$name]['length'];
            $endline = $line + $length;

            ## when last line is like "}}" then separate it to new line
            $lastline = trim($this->file[$endline - 1]);
            if (substr($lastline, -2) == "}}") {
                $lastline[strlen($lastline) - 1] = " ";
                $this->file[$endline - 1] = $lastline;
                $this->file[] = "\n";
                $this->file[] = "}";
            }
        }

        $default = [
            'visibility' => 'public',
            'params'     => []
        ];
        $options = array_merge($default, $options);
        $params = implode(",", $options['params']);

        $func = <<<EOF
    {$default['visibility']} function {$name}({$params}) {
{$body}
    }
EOF;

        array_splice($this->file, $line, $length, explode("\n", $func));

        ## adjust other methods line and length
        $newlength = count(explode("\n", $func));

        foreach ($this->methods as $k => $m) {
            if ($m['line'] >= $line) {
                if (!$isNewFunc) {
                    $this->methods[$k]['line'] -= $length;
                }

                $this->methods[$k]['line'] += $newlength;
            }
        }


        $this->methods[$name] = [
            'line'   => $line,
            'length' => $newlength
        ];
        $this->updateSession('JUSTWRITTEN');

        $this->save();
    }

    protected function save() {

        $fp = fopen($this->filePath, 'r+');
        ## write new function to sourceFile
        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
            ftruncate($fp, 0); // truncate file
            fwrite($fp, implode("\n", $this->file));
            fflush($fp); // flush output before releasing the lock
            flock($fp, LOCK_UN); // release the lock
        } else {
            throw new Exception("ERROR: Couldn't lock source file '{$this->filePath}'!");
        }
    }

}
