<?php

/**
 * Class DropDownList
 * @author rizky
 */
class RelationField extends FormField {

    /** @var string $toolbarName */
    public static $toolbarName = "Relation Field";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-link";

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name             = '';
    public $relationCriteria = [
        'select'    => '',
        'distinct'  => 'false',
        'alias'     => 't',
        'condition' => '{[search]}',
        'order'     => '',
        'group'     => '',
        'having'    => '',
        'join'      => ''
    ];
    public $identifier       = '';
    public $params           = [];

    /** @var string $value digunakan pada function checked */
    public $value = '';

    /** @var array $options */
    public $options = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var string $list */
    public $list = '';

    /** @var string $listExpr digunakan pada function processExpr */
    public $listExpr     = '';
    public $includeEmpty = 'No';
    public $emptyValue   = '';
    public $emptyLabel   = '-- NONE --';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var string $searchable */
    public $searchable = 'No';

    /** @var string $showOther */
    public $showOther    = 'No';
    public $showUnselect = 'No';

    /** @var string $otherLabel */
    public $otherLabel = 'Lainnya';
    public $modelClass = '';
    public $idField    = '';
    public $labelField = '';
    public $menuPos    = '';
    public $queryParams;

    public static function listModel($includeEmpty = false) {
        $devDir = Yii::getPathOfAlias("application.models");
        $appDir = Yii::getPathOfAlias("app.models");

        $devItems = glob($devDir . DIRECTORY_SEPARATOR . "*");
        $appItems = glob($appDir . DIRECTORY_SEPARATOR . "*");

        $items = [];

        if ($includeEmpty) {
            $items[''] = ' -- NONE -- ';
        }

        foreach ($appItems as $k => $m) {
            $mm = $m;
            $m  = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m  = str_replace('.php', "", $m);
            if (is_dir($mm)) {
                $dirs = glob($mm . DIRECTORY_SEPARATOR . "*");
                foreach ($dirs as $d => $n) {
                    $n = str_replace($mm . DIRECTORY_SEPARATOR, "", $n);
                    $n = str_replace('.php', "", $n);

                    $items[$m]['app.models.' . $m . "." . $n] = $n;
                }
            } else {
                $items['Application']['app.models.' . $m] = $m;
            }
        }

        foreach ($devItems as $k => $m) {
            $m = str_replace($devDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $items['Plansys']['application.models.' . $m] = $m;
        }

        return $items;
    }

    /**
     * @return array me-return array property DropDown.
     */
    public function getFieldProperties() {
        return array(
            array(
                'label'      => 'Field Name',
                'name'       => 'name',
                'options'    => array(
                    'ng-model'  => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list'   => 'modelFieldList',
                ),
                'list'       => array(),
                'searchable' => 'Yes',
                'showOther'  => 'Yes',
                'type'       => 'DropDownList',
            ),
            array(
                'label'      => 'Model Class',
                'name'       => 'modelClass',
                'options'    => array(
                    'ng-model'  => 'active.modelClass',
                    'ng-change' => 'generateRelationField();save();',
                ),
                'menuPos'    => 'pull-right',
                'listExpr'   => 'RelationField::listModel()',
                'searchable' => 'Yes',
                'type'       => 'DropDownList',
            ),
            array(
                'label'      => 'ID Field',
                'name'       => 'idField',
                'options'    => array(
                    'ng-model'  => 'active.idField',
                    'ng-change' => 'save();',
                    'ps-list'   => 'relationFieldList',
                ),
                'list'       => array(),
                'searchable' => 'Yes',
                'showOther'  => 'Yes',
                'otherLabel' => 'Custom',
                'type'       => 'DropDownList',
            ),
            array(
                'label'      => 'Label Field',
                'name'       => 'labelField',
                'options'    => array(
                    'ng-model'  => 'active.labelField',
                    'ng-change' => 'save();',
                    'ps-list'   => 'relationFieldList',
                ),
                'list'       => array(),
                'searchable' => 'Yes',
                'showOther'  => 'Yes',
                'otherLabel' => 'Custom',
                'type'       => 'DropDownList',
            ),
            array(
                'label'   => 'Field Identifier',
                'name'    => 'identifier',
                'prefix'  => 'js: rel.',
                'options' => array(
                    'ng-model'  => 'active.identifier',
                    'ng-change' => 'save();',
                    'ng-delay'  => '500',
                ),
                'type'    => 'TextField',
            ),
            array(
                'name'         => 'relationCriteria',
                'label'        => 'Sql Criteria',
                'paramsField'  => 'params',
                'baseClass'    => 'RelationField',
                'options'      => array(
                    'ng-model'  => 'active.relationCriteria',
                    'ng-change' => 'save();',
                ),
                'modelClassJS' => 'RelationField/relation-criteria.js',
                'type'         => 'SqlCriteria',
            ),
            array(
                'label'   => 'Sql Parameters',
                'name'    => 'params',
                'show'    => 'Show',
                'options' => array(
                    'ng-model'  => 'active.params',
                    'ng-change' => 'save();',
                ),
                'type'    => 'KeyValueGrid',
            ),
            array(
                'type'  => 'Text',
                'value' => '<hr/>',
            ),
            array(
                'label'      => 'Include Empty',
                'name'       => 'includeEmpty',
                'options'    => array(
                    'ng-model'  => 'active.includeEmpty',
                    'ng-change' => 'save();',
                ),
                'listExpr'   => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type'       => 'DropDownList',
            ),
            array(
                'label'   => 'Empty Value',
                'name'    => 'emptyValue',
                'options' => array(
                    'ng-model'  => 'active.emptyValue',
                    'ng-change' => 'save()',
                    'ng-show'   => 'active.includeEmpty == \'Yes\'',
                    'ng-delay'  => '500',
                ),
                'type'    => 'TextField',
            ),
            array(
                'label'   => 'Empty Label',
                'name'    => 'emptyLabel',
                'options' => array(
                    'ng-model'  => 'active.emptyLabel',
                    'ng-change' => 'save()',
                    'ng-show'   => 'active.includeEmpty == \'Yes\'',
                    'ng-delay'  => '500',
                ),
                'type'    => 'TextField',
            ),
            array(
                'type'  => 'Text',
                'value' => '<hr/>',
            ),
            array(
                'label'   => 'Label',
                'name'    => 'label',
                'options' => array(
                    'ng-model'  => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay'  => 500,
                ),
                'type'    => 'TextField',
            ),
            array(
                'label'      => 'Layout',
                'name'       => 'layout',
                'options'    => array(
                    'ng-model'  => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr'   => 'array(\'Horizontal\',\'Vertical\')',
                'fieldWidth' => '6',
                'type'       => 'DropDownList',
            ),
            array(
                'label'      => 'Menu Position',
                'name'       => 'menuPos',
                'options'    => array(
                    'ng-model'  => 'active.menuPos',
                    'ng-change' => 'save();',
                ),
                'listExpr'   => '[\'\'=>\'Left\',\'pull-right\'=>\'Right\']',
                'fieldWidth' => '4',
                'type'       => 'DropDownList',
            ),
            array(
                'label'      => 'Show Unselect',
                'name'       => 'showUnselect',
                'options'    => array(
                    'ng-model'  => 'active.showUnselect',
                    'ng-change' => 'save();',
                ),
                'listExpr'   => '[\'No\', \'Yes\']',
                'fieldWidth' => '4',
                'type'       => 'DropDownList',
            ),
            array(
                'type'  => 'Text',
                'value' => '<hr/>',
            ),
            array(
                'totalColumns' => '4',
                'column1'      => array(
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2'      => array(
                    array(
                        'label'      => 'Label Width',
                        'name'       => 'labelWidth',
                        'layout'     => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options'    => array(
                            'ng-model'    => 'active.labelWidth',
                            'ng-change'   => 'save()',
                            'ng-delay'    => 500,
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type'       => 'TextField',
                    ),
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column3'      => array(
                    array(
                        'label'      => 'Field Width',
                        'name'       => 'fieldWidth',
                        'layout'     => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options'    => array(
                            'ng-model'  => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay'  => 500,
                        ),
                        'type'       => 'TextField',
                    ),
                    array(
                        'type'  => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1'           => '25%',
                'w2'           => '25%',
                'w3'           => '25%',
                'w4'           => '25%',
                'type'         => 'ColumnField',
            ),
            array(
                'label' => 'Options',
                'name'  => 'options',
                'type'  => 'KeyValueGrid',
            ),
            array(
                'label' => 'Label Options',
                'name'  => 'labelOptions',
                'type'  => 'KeyValueGrid',
            ),
            array(
                'label' => 'Field Options',
                'name'  => 'fieldOptions',
                'type'  => 'KeyValueGrid',
            ),
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['relation-field.js'];
    }

    public function actionDgrInit() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        if (count($post) == 0)
            die();

        $attr = [];
        if (isset($post['model_id'])) {
            $attr['id'] = $post['model_id'];
        }

        $fb            = FormBuilder::load($post['class'], $attr);
        $ff            = $fb->findField(['name' => $post['name']]);
        $this->builder = $fb;
        $return        = [];
        foreach ($post['cols'] as $alias => $ids) {
            $return[$alias] = [];
            $fc             = null;
            foreach ($ff['columns'] as $ffc) {
                if ($ffc['columnType'] == "relation" && $ffc['name'] == $alias) {
                    $fc = $ffc;
                    break;
                }
            }
            if (!is_null($fc)) {
                if (count($ids) > 0) {
                    $this->modelClass       = $fc['relModelClass'];
                    $this->idField          = $fc['relIdField'];
                    $this->labelField       = $fc['relLabelField'];
                    $this->relationCriteria = @$fc['relCriteria'];
                    $this->params           = @$fc['relParams'];
                    foreach ($ids as $k => $i) {
                        $ids[$k] = (is_string($i) ? '"' . $i . '"' : $i);
                    }
                    $ids = implode(" , ", $ids);

                    $this->relationCriteria['nolimit'] = true;

                    if (@$this->relationCriteria['alias'] != "") {
                        $id = $this->relationCriteria['alias'] . "." . $this->idField;
                    } else {
                        $id = $this->idField;
                    }

                    if (@$this->relationCriteria['condition'] != "") {
                        $c = $this->relationCriteria['condition'];
                        if (trim(str_replace("{[search]}", "", $c)) != '') {
                            $c = "(" . $c . ") {AND} ({$id} IN ({$ids}))";
                        } else {
                            $c = "({$id} IN ({$ids}))";
                        }
                        $this->relationCriteria['condition'] = $c;
                    } else {
                        $this->relationCriteria['condition'] = "({$id} IN ({$ids}))";
                    }

                    $result = $this->query('', is_null($this->params) ? [] : $this->params);

                    foreach ($result as $i => $r) {
                        $return[$alias] = $result;
                    }
                }
            }
        }
        echo json_encode($return);
    }

    public function query($search = '', $params = [], $initialID = null) {
        Yii::import($this->modelClass);

        $class = Helper::explodeLast(".", $this->modelClass);
        if (!class_exists($class))
            return;

        $model    = new $class;
        $table    = $model->tableName();
        $criteria = $this->generateCriteria($search, $params);
        $rawlist  = $model->currentModel($criteria);

        if (!is_null($initialID) && $initialID != "") {
            $found = false;

            foreach ($rawlist as $r) {
                if (!isset($r[$this->idField])) {
                    continue;
                }

                if ($r[$this->idField] == $initialID)
                    $found = true;
            }

            if (!$found) {
                $t                     = $criteria['alias'];
                $criteria['condition'] = "{$t}.{$this->idField} = '{$initialID}'";
                $initial               = $model->currentModel($criteria);

                if (!empty($initial)) {
                    ## remove duplicate label
                    foreach ($rawlist as $k => $r) {
                        if (!is_null(@$r[$this->labelField]) && @$r[$this->labelField] == @$initial[0][$this->labelField]) {
                            unset($rawlist[$k]);
                        }
                    }

                    $rawlist = array_merge($rawlist, $initial);
                }
            }
        }

        $list = [];
        foreach ($rawlist as $k => $i) {
            $included = true;
            if ($included) {
                $field = array_keys($i);

                if (in_array($this->idField, $field)) {
                    $value = $i[$this->idField];
                } else {
                    $value = @$this->evaluate("\"" . str_replace("{", "{\$", $this->idField) . "\"", true, $i);
                }

                if (in_array($this->labelField, $field)) {
                    $label = $i[$this->labelField];
                } else {
                    $label = @$this->evaluate("\"" . str_replace("{", "{\$", $this->labelField) . "\"", true, $i);
                }

                $list[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        if ($this->includeEmpty == 'Yes') {
            if (count($list) > 0) {
                array_unshift($list, [
                    'value' => '---',
                    'label' => '---'
                ]);
            }
            array_unshift($list, [
                'value' => $this->emptyValue,
                'label' => $this->emptyLabel
            ]);
        }

        $this->list = $list;
        return $list;
    }

    public function generateCriteria($search, $params) {
        $condition = $this->generateCondition($search, $params);

        $nolimit                             = false;
        $this->relationCriteria['condition'] = $condition["sql"];

        $this->params = array_merge(is_null($this->params) ? [] : $this->params, $condition['params']);
        $criteria     = DataSource::generateCriteria($this->params, $this->relationCriteria, $this);

        ##clean criteria condition
        if (isset($criteria['condition'])) {
            if (stripos($criteria['condition'], "and ") === 0) {
                $criteria['condition'] = substr($criteria['condition'], 4);
            }
            if (stripos($criteria['condition'], " and") === strlen($criteria['condition']) - 4) {
                $criteria['condition'] = substr($criteria['condition'], 0, strlen($criteria['condition']) - 4);
            }
            if (stripos($criteria['condition'], "or ") === 0) {
                $criteria['condition'] = substr($criteria['condition'], 3);
            }
            if (stripos($criteria['condition'], " or") === strlen($criteria['condition']) - 3) {
                $criteria['condition'] = substr($criteria['condition'], 0, strlen($criteria['condition']) - 3);
            }
        }

        if (array_key_exists('page', $criteria)) {
            $start             = ($criteria['page'] - 1) * $criteria['pageSize'];
            $pageSize          = $criteria['pageSize'];
            $criteria['limit'] = $pageSize;
            if (!isset($criteria['offset'])) {
                $criteria['offset'] = $start;
            }

            unset($criteria['pageSize']);
            unset($criteria['page']);
        }

        if ($nolimit) {
            if (isset($criteria['limit'])) {
                unset($criteria['limit']);
            }
            if (isset($criteria['offset'])) {
                unset($criteria['offset']);
            }
            $criteria['nolimit'] = true;
        }

        ## softDelete behavior
        $this->applySoftDelete($criteria);

        return $criteria;
    }

    public function generateCondition($search = '', &$jsparams = []) {
        $class  = explode(".", $this->modelClass);
        $class  = end($class);
        $model  = new $class;
        $driver = $model->dbConnection->driverName;
        
        $sql = @$this->relationCriteria['condition'] ? $this->relationCriteria['condition'] : "";

        preg_match_all("/\[(.*?)\]/", $sql, $blocks);
        preg_match_all("/\{(.*?)\}/", $this->labelField, $fields);

        if ($search != '') {
            if (count($fields[1]) == 0) {
                $fields[1] = [$this->labelField];
            } else {
                $rf  = explode(",", @$this->relationCriteria['select']);
                $als = [];
                if (count($rf) > 0) {
                    foreach ($rf as $key => $v) {
                        if (stripos($v, " as ") !== false) {
                            $t = explode(" as ", $v);
                            if (count($t) < 2) {
                                $t = preg_split("/ as /i", $v);
                            }
                            $als[trim($t[1])] = trim($t[0]);
                        }
                    }
                }
                foreach ($fields[1] as $key => $v) {

                    if (isset($als[$v])) {
                        $fields[1][$key] = $als[$v];
                    } else {
                        if (strpos($v, ".") !== false) {
                            $fields[1][$key] = $v;
                        } else {
                            $fields[1][$key] = $this->relationCriteria['alias'] . "." . $v;
                        }
                    }
                }
            }

            if (count($blocks[1]) == 0) {
                $blocks[1] = ['search'];
                $sql       = "where [search]";
            }
        }

        ## generate parameters
        $params = [];
        foreach ($blocks[1] as $k => $block) {
            $cond = '';

            ## usage: "where [search]", [search] = search term
            if (strpos($block, 'search') !== false) {
                if ($search != '') {
                    if (strpos($this->labelField, '{') !== false) {
                        $alias  = $this->relationCriteria['alias'];
                        $select = $this->relationCriteria['select'];
                        $field  = $this->labelField;

                        // generate translasi alias
                        $x     = [];
                        $trans = explode(',', $select);
                        foreach ($trans as $k => $t) {
                            $list  = explode(" ", trim($t));
                            $li    = array_shift($list);
                            $liOri = $li;

                            $x[$li] = $li;

                            // pemisah alias
                            $ds = explode(".", $li);
                            if (count($ds) > 1) {
                                if (!isset($x[$ds[count($ds) - 1]])) {
                                    $x[$ds[count($ds) - 1]] = $li;
                                }
                            } else {
                                $x[$li]                = $alias . "." . $li;
                                $x[$alias . '.' . $li] = $alias . '.' . $li;
                            }

                            // jika ada AS
                            if (count($list) > 0) {
                                $li = array_pop($list);

                                //jika pake alias
                                if (count($ds) > 1) {
                                    $x[$li] = $liOri;
                                } else {
                                    $x[$li] = $alias . "." . $liOri;
                                }
                            }
                        }

                        // replace kurung kurawal
                        preg_match_all("/\{(.*?)\}/", $field, $fieldArray);
                        $hasil = $field;

                        foreach ($fieldArray[0] as $k => $f) {
                            if (@$x[$fieldArray[1][$k]]) {
                                switch ($driver) {
                                    case "oci":
                                        $hasil = str_replace($f, '{LOWER(COALESCE(TO_CHAR(' . $x[$fieldArray[1][$k]] . '), \'\'))}', $hasil);
                                        break;
                                    default:
                                        $hasil = str_replace($f, '{LOWER(IFNULL(' . $x[$fieldArray[1][$k]] . ', ""))}', $hasil);
                                }
                            } else {
                                switch ($driver) {
                                    case "oci":
                                        $hasil = str_replace($f, '{LOWER(COALESCE(TO_CHAR(' . $fieldArray[1][$k] . '), \'\'))}', $hasil);
                                        break;
                                    default:
                                        $hasil = str_replace($f, '{LOWER(IFNULL(' . $fieldArray[1][$k] . ', ""))}', $hasil);
                                }
                            }
                        }

                        switch ($driver) {
                            case "oci":
                                $hasil = str_replace('{', "' || ", $hasil);
                                $hasil = str_replace('}', " || '", $hasil);
                                break;
                            default:
                                $hasil = str_replace('{', "',", $hasil);
                                $hasil = str_replace('}', ",'", $hasil);
                        }

                        if ($hasil[0] == "'") {
                            $hasil = substr($hasil, 2);
                        } else {
                            $hasil = "'" . $hasil;
                        }
                        if ($hasil[strlen($hasil) - 1] == "'") {
                            $hasil = substr($hasil, 0, strlen($hasil) - 2);
                        } else {
                            $hasil = $hasil . "'";
                        }

                        switch ($driver) {
                            case "oci":
                                $hasil = trim($hasil);
                                $hasil = trim($hasil, "||");
                                $sqlcond = $hasil . ' like ' . "LOWER([{$block}])";
                                break;
                            default:
                                $sqlcond = "CONCAT(" . $hasil . ")" . ' like ' . "LOWER([{$block}])";
                        }
                    } else {
                        switch ($driver) {
                            case "oci":
                                $sqlcond = "LOWER(|" . $this->relationCriteria['alias'] . "|.|" . $this->labelField . '|) like ' . "LOWER([{$block}])";

                                break;
                            default:
                                $sqlcond = "LOWER(" . $this->labelField . ') like ' . "LOWER([{$block}])";
                        }
                    }

                    $search         = preg_replace('!\s+!', '%', trim($search));
                    $sql            = str_replace("[search]", $sqlcond, $sql);
                    $params[$block] = "%{$search}%";
                } else {
                    $params[$block] = null;
                }
            } ## usage: "where user_id = {$model->id}",  $model = current form activerecord
            else if (strpos($block, '$model') !== false) {
                preg_match("/\\\$model->[\w_]+/", $block, $modelVar);
                foreach ($modelVar as $v) {
                    $val = $this->evaluate("\"{$v}\"", true);
                    if ($val == '') {
                        $cond = '';
                    } else {
                        $cond = $val;
                    }
                }
                $params[$block] = $cond;
            }
        }

        ## remove empty-valued conditional curly braces
        preg_match_all("/\{(.*?)\}/", $sql, $curlies);
        foreach ($curlies[0] as $c => $curly) {
            foreach ($params as $k => $p) {
                if (strpos($curly, '[' . $k . ']') !== false) {
                    if (!$p) {
                        $sql = str_replace($curly, '', $sql);
                        unset($params[$k]);
                    } else {
                        $sql = str_replace($curly, $curlies[1][$c], $sql);
                    }
                }
            }
        }

        ## assemble parameters
        $i            = 0;
        $returnParams = [];
        foreach ($params as $k => $p) {
            $returnParams[':param_' . $i] = "'" . $p . "'";
            $sql                          = str_replace('[' . $k . ']', ':param_' . $i, $sql);
            $i++;
        }
        $returnParams = array_merge($returnParams, $jsparams);

        ## trim and or, etc
        return [
            'sql'    => $sql,
            'params' => $returnParams
        ];
    }

    public function actionDgrSearch() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        $s = '';
        $m = '';
        $f = '';
        extract($post);

        $fb    = FormBuilder::load($m);
        $field = $fb->findField(['name' => $f]);

        foreach ($field['columns'] as $column) {
            if ($c == $column['name']) {
                $this->modelClass       = @$column['relModelClass'];
                $this->idField          = @$column['relIdField'];
                $this->labelField       = @$column['relLabelField'];
                $this->relationCriteria = @$column['relCriteria'];
                $this->params           = is_null(@$column['relParams']) ? [] : $column['relParams'];

                if (is_array(@$this->params)) {
                    foreach ($this->params as $k => $ff) {
                        if (substr($ff, 0, 3) == "js:" && isset($p[$k])) {
                            if (is_string($p[$k])) {
                                $this->params[$k] = "'" . $p[$k] . "'";
                            }
                        }
                    }
                }
            }
        }
        $this->builder = $fb;
        echo json_encode($this->query(@$s, $this->params));
    }

    public function actionGetDetail($m, $id, $fd) {
        if (strpos($m, ".") > 0) {
            Yii::import($m);
            $m = Helper::explodeLast(".", $m);
        }

        if (class_exists($m)) {
            $data = $m::model()->findByAttributes([$fd => $id]);
            $data->loadAllRelations();
            echo json_encode($data->attributes);
        }
    }

    public function actionFindId() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        if (!is_array($post)) {
            return;
        }

        $m = '';
        $f = '';
        $v = '';
        $s = '';
        $p = '';
        extract($post);

        $fb = FormBuilder::load($m);
        if (isset($i)) {
            $field = $fb->findField(['name' => $f, 'identifier' => @$i]);
        } else {
            $field = $fb->findField(['name' => $f]);
        }

        $this->attributes = $field;
        $this->builder    = $fb;

        if (@$field['criteria']) {
            $this->relationCriteria = @$field['criteria'];
        }

        if (is_array(@$field['params'])) {
            foreach ($field['params'] as $k => $ff) {
                if (substr($ff, 0, 3) == "js:" && isset($p[$k])) {
                    if (is_string($p[$k])) {
                        $p[$k] = "'" . $p[$k] . "'";
                    }
                }
            }
        }


        $this->relationCriteria['condition'] = $this->relationCriteria['alias'] . "." . $this->idField . " = :find_id_param_relation_field";
        $p[':find_id_param_relation_field']  = "php:'" . $v . "'";

        $result = $this->query($s, $p);
        echo json_encode(@$result[count($result) - 1]);
    }

    private function applySoftDelete(&$criteria) {
        Yii::import($this->modelClass);
        $relClass = Helper::explodeLast(".", $this->modelClass);
        if (!class_exists($relClass))
            return;

        $sd = $relClass::model()->_softDelete;
        if (!empty($sd)) {
            $prefix = '';
            if (@$criteria['alias'] != '') {
                $prefix = $criteria['alias'] . '.';
            }

            if (!isset($criteria['condition'])) {
                $criteria['condition'] = '';
                $and                   = '';
            } else {
                $and = ' AND ';
            }

            $criteria['condition'] .= "{$and} {$prefix}{$sd['column']} <> '{$sd['value']}'";
        }
    }

    public function actionSearch() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        if (!is_array($post)) {
            return;
        }

        $m = '';
        $f = '';
        $v = '';
        $s = '';
        $p = '';
        extract($post);

        $fb = FormBuilder::load($m);
        if (isset($i)) {
            $field = $fb->findField(['name' => $f, 'identifier' => @$i]);
        } else {
            $field = $fb->findField(['name' => $f]);
        }


        $this->attributes = $field;
        $this->builder    = $fb;

        if (@$field['criteria']) {
            $this->relationCriteria = @$field['criteria'];
        }
        $this->relationCriteria['limit']  = ActiveRecord::DEFAULT_PAGE_SIZE;
        $this->relationCriteria['offset'] = 0;
        if (isset($start)) {
            $this->includeEmpty               = 'No';
            $this->relationCriteria['offset'] = $start;
        }

        if ($this->modelClass == '') {
            $this->modelClass = $m;
        }

        if (is_array(@$field['params'])) {
            foreach ($field['params'] as $k => $ff) {
                if (substr($ff, 0, 3) == "js:" && isset($p[$k])) {
                    if (is_string($p[$k])) {
                        $p[$k] = "'" . $p[$k] . "'";
                    }
                }
            }
        }

        echo json_encode([
            'list'  => $this->query($s, $p),
            'count' => $this->count($s, $p)
        ]);
    }

    public function count($search = '', $params = []) {
        Yii::import($this->modelClass);

        $class = Helper::explodeLast(".", $this->modelClass);
        if (!class_exists($class))
            return;

        $model       = new $class;
        $tableSchema = $model->tableSchema;
        $builder     = $model->commandBuilder;
        $criteria    = $this->generateCriteria($search, $params);
        $validSelect = (isset($criteria['select']) && strpos($criteria['select'], ',') === false);

        if ($criteria['distinct']) {
            $criteria['distinct'] = false;

            if ($validSelect) {
                $criteria['select'] = 'COUNT(DISTINCT ' . $criteria['select'] . ')';
            } else {
                $criteria['select'] = 'COUNT(1)';
            }
        }

        if (isset($criteria['paging']))
            unset($criteria['paging']);

        if (isset($criteria['page']))
            unset($criteria['page']);

        if (isset($criteria['pageSize']))
            unset($criteria['pageSize']);

        if (isset($criteria['limit']))
            unset($criteria['limit']);

        if (isset($criteria['offset']))
            unset($criteria['offset']);

        $this->applySoftDelete($criteria);
        $countCommand = $builder->createCountCommand($tableSchema, new CDbCriteria($criteria));
        $count        = $countCommand->queryScalar();

        return $count;
    }

    public function actionlistFieldByRel() {
        if (!@$_GET['class'] && !@$_GET['rel']) {
            echo json_encode([]);
            die();
        }

        $class = $_GET['class'];
        Yii::import($class);
        $class = Helper::explodeLast(".", $class);
        $model = new $class;

        if (isset($model->metaData->relations[$_GET['rel']])) {
            $rel      = $model->metaData->relations[$_GET['rel']];
            $relClass = $rel->className;
            $relModel = $relClass::model();

            $data = $relModel::model()->attributesList;
            unset($data['Relations']);
            unset($data['Properties']);
            echo json_encode($data);
        } else {
            echo json_encode([]);
            die();
        }
    }

    public function actionListField() {
        if (!@$_GET['class']) {
            echo json_encode([]);
            die();
        }

        $class = $_GET['class'];

        Yii::import($class);
        $class = Helper::explodeLast(".", $class);
        $model = new $class;
        $data  = [];
        if (is_subclass_of($model, 'ActiveRecord')) {
            $formType = "ActiveRecord";
            $data     = $class::model()->attributesList;
            unset($data['Relations']);
            unset($data['Properties']);
        } else if (is_subclass_of($model, 'FormField')) {
            $formType = "FormField";
            $mf       = new $class;
            $data     = $mf->attributes;
            unset($data['type']);
        } else if (is_subclass_of($model, 'Form')) {
            $formType = "Form";
            $mf       = new $class;
            $data     = $mf->attributes;
            unset($data['type']);
        }

        echo json_encode($data);
    }

    /**
     * checked
     * Fungsi ini untuk mengecek value dari field
     * @param string $value
     * @return boolean me-return true atau false
     */
    public function checked($value) {
        if ($this->value == $value)
            return true;
        else
            return false;
    }

    /**
     * getLayoutClass
     * Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout
     * @return string me-return string Class layout yang digunakan
     */
    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    /**
     * @return string me-return string Class error jika terdapat error pada satu atau banyak attribute.
     */
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    /**
     * getlabelClass
     * Fungsi ini akan mengecek $layout untuk menentukan layout yang digunakan
     * dan meload option label dari property $labelOptions
     * @return string me-return string Class label
     */
    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    /**
     * @return string me-return string class
     */
    public function getFieldClass() {
        return "btn-group btn-block";
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field DropDownList dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('btn dropdown-toggle btn-sm btn-block btn-dropdown-field', 'fieldOptions');
        $btn_class = ['btn-primary', 'btn-default', 'btn-success', 'btn-danger', 'btn-warning'];
        if (!in_array($this->fieldOptions['class'], $btn_class)) {
            $this->addClass('btn-default', 'fieldOptions');
        }
        $this->fieldOptions['ng-init'] = 'initScopeItem(this,$index)';
        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);

        $this->relationCriteria['limit']  = ActiveRecord::DEFAULT_PAGE_SIZE;
        $this->relationCriteria['offset'] = '0';
        $this->query('', [], $this->value);

        return $this->renderInternal('template_render.php');
    }

}
