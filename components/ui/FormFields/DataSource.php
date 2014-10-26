<?php

/**
 * Class DataSource
 * @author rizky
 */
class DataSource extends FormField {

    /**
     * @return array Fungsi ini akan me-return array property DataSource.
     */
    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Data Source Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Post Data ?',
                'name' => 'postData',
                'options' => array(
                    'ng-model' => 'active.postData',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Relation To',
                'name' => 'relationTo',
                'options' => array(
                    'ng-model' => 'active.relationTo',
                    'ng-change' => 'save()',
                    'ps-list' => 'relFieldList',
                    'ng-if' => 'active.postData == \\\'Yes\\\'',
                ),
                'list' => array(),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'otherLabel' => '-- NONE --',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Debug SQL ?',
                'name' => 'debugSql',
                'options' => array(
                    'ng-model' => 'active.debugSql',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.relationTo == \\\'\\\' || active.postData == \\\'No\\\'',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Source Type',
                'name' => 'fieldType',
                'options' => array(
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.relationTo == \\\'\\\' || active.postData == \\\'No\\\'',
                ),
                'list' => array(
                    'sql' => 'SQL',
                    'php' => 'PHP Function',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Paging',
                'name' => 'enablePaging',
                'options' => array(
                    'ng-model' => 'active.enablePaging',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.relationTo == \\\'\\\' || active.postData == \\\'No\\\'',
                ),
                'list' => array(),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'name' => 'relationCriteria',
                'label' => 'Relation Query',
                'paramsField' => 'params',
                'baseClass' => 'DataSource',
                'options' => array(
                    'ng-if' => 'active.postData == \\\'Yes\\\' && active.relationTo != \\\'\\\'',
                    'ng-model' => 'active.relationCriteria',
                    'ng-change' => 'save()',
                ),
                'modelClassJS' => 'DataSource/relation-criteria.js',
                'type' => 'SqlCriteria',
            ),
            array(
                'label' => 'SQL',
                'fieldname' => 'sql',
                'language' => 'sql',
                'options' => array(
                    'ng-show' => 'active.fieldType == \\\'sql\\\' && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'PHP Function',
                'fieldname' => 'php',
                'options' => array(
                    'ng-show' => 'active.fieldType == \\\'php\\\' && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Total Item - PHP Function',
                'fieldname' => 'pagingPHP',
                'options' => array(
                    'ng-show' => 'active.fieldType == \\\'php\\\' && active.enablePaging == \\\'Yes\\\' && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Total Item - SQL',
                'fieldname' => 'pagingSQL',
                'language' => 'sql',
                'options' => array(
                    'ng-show' => 'active.fieldType == \\\'sql\\\' && active.enablePaging == \\\'Yes\\\' && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Parameters',
                'name' => 'params',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $name */
    public $name = '';

    /** @var string $fieldType */
    public $fieldType = 'sql';

    /** @var string $sql */
    public $sql = '';

    /** @var string $php */
    public $php = '';
    public $postData = 'Yes';

    /** @var string $params */
    public $params = array();
    private $postedParams = array();

    /** @var string $data */
    public $data;
    public $debugSql = 'No';
    public $enablePaging = 'No';
    public $pagingSQL = '';
    public $pagingPHP = '';
    public $relationTo = '';
    public $relationCriteria = array(
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[where]}',
        'order' => '{[order]}',
        'paging' => '{[paging]}',
        'group' => '',
        'having' => '',
        'join' => ''
    );
    private $command;

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";
    private $queryParams = array();

    public function actionRelClass() {
        Yii::import($_GET['class']);
        $class = array_pop(explode(".", $_GET['class']));

        $relClass = '';
        if (@$_GET['rel'] == 'currentModel') {
            $relClass = $class;
        } else {
            $model = new $class;
            $rels = $model->relations();
            $relClass = @$rels[$_GET['rel']][1];
        }

        echo Helper::classAlias($relClass);
    }

    public function actionQuery() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $class = array_pop(explode(".", $post['class']));
        Yii::import($post['class']);

        if (class_exists($class)) {
            $fb = FormBuilder::load($class);
            $fb->model = $class::model()->findByPk(@$post['model_id']);
            if (is_null($fb->model)) {
                $fb->model = new $class;
            }

            $field = $fb->findField(array('name' => $post['name']));
            $this->queryParams = (is_array(@$post['params']) ? @$post['params'] : array());
            $this->attributes = $field;
            $this->builder = $fb;

            $isGenerate = isset($post['generate']);

            if (is_string($this->params)) {
                $this->params = array();
            }

            if ($this->relationTo == '' || $this->relationTo == '-- NONE --') {
                ## without relatedTo

                if ($this->fieldType == 'sql') {
                    $data = $this->query($this->params);
                } else {
                    $data = $this->execute($this->params);
                }
            } else {
                ## with relatedTo
                $data = $this->getRelated($this->params, $isGenerate);
            }

            echo json_encode(array(
                'data' => $data['data'],
                'count' => $data['debug']['count'],
                'params' => $data['debug']['params'],
                'debug' => ($this->debugSql == 'Yes' ? $data['debug'] : array())
            ));
        }
    }

    protected static function processSQLBracket($sql, $postedParams, $field) {
        preg_match_all("/\[(.*?)\]/", $sql, $matches);
        $params = $matches[1];
        $parsed = array();

        foreach ($params as $param) {
            $template = $sql;
            $paramOptions = explode("|", $param);
            $param = array_shift($paramOptions);

            if (!isset($field->params[$param])) {
                $sql = str_replace("[{$param}]", "", $sql);
                $parsed[$param] = "";
                continue;
            }

            switch ($param) {
                case "where":
                    $fieldSql = 'DataFilter::generateParams($paramName, $params, $template, $paramOptions)';
                    break;
                case "order":
                case "paging":
                    $fieldSql = 'DataGrid::generateParams($paramName, $params, $template, $paramOptions)';
                    break;
                default:
                    $ff = $field->builder->findField(array('name' => $field->params[$param]));
                    $fieldSql = @$ff['options']['ps-ds-sql'];
                    break;
            }

            if (isset($fieldSql)) {
                $template = $field->evaluate($fieldSql, true, array(
                    'paramName' => $param,
                    'params' => @$postedParams[$param],
                    'template' => $template,
                    'paramOptions' => $paramOptions
                ));

                if (!isset($template['generateTemplate'])) {
                    $sql = str_replace("[{$param}]", $template['sql'], $sql);
                } else {
                    $sql = $template['sql'];
                }

                if ($template['sql'] != '') {
                    $parsed[$param] = $template['params'];
                }

                if (isset($template['render'])) {
                    return array('sql' => $sql, 'params' => $parsed, 'render' => $template['render']);
                }
            }
        }
        return array('sql' => $sql, 'params' => $parsed);
    }

    public function getPostName($mode = '') {
        if ($this->relationTo == '' || $this->relationTo == 'currentModel') {
            return $this->name . $mode;
        } else {
            $name = str_replace($this->name, $this->relationTo, $this->renderName);

            if ($mode != '') {
                $name = substr_replace($name, $mode . ']', -1);
            }
            return $name;
        }
    }

    public static function concatSql($sql, $operator) {
        $andsql = array_filter(preg_split("/\{" . $operator . "\}/i", $sql), function($e) {
            return (trim($e) != "" ? trim($e) : false);
        });
        $sql = implode(" " . $operator . " ", $andsql);
        return $sql;
    }

    public static function generateTemplate($sql, $postedParams = array(), $field) {
        $returnParams = array();

        ## find all params
        preg_match_all("/\:[\w\d_]+/", $sql, $params);
        $model = $field->model;
        foreach ($params[0] as $p) {
            if (isset($postedParams[$p])) {
                if (strpos($postedParams[$p], 'js:') !== false) {
                    switch (get_class($field)) {
                        case "DataSource":
                            $returnParams[$p] = @$field->queryParams[$p];
                            break;
                        default:
                            $returnParams[$p] = '';
                            break;
                    }
                } else {

                    $returnParams[$p] = $field->evaluate($postedParams[$p], true, array(
                        'model' => $model
                    ));
                }
            }
        }

        ## find all blocks
        preg_match_all("/\{(.*?)\}/", $sql, $blocks);

        foreach ($blocks[1] as $block) {
            if (strtolower($block) == "and" || strtolower($block) == "or") {
                continue;
            }

            $bracket = DataSource::processSQLBracket($block, $postedParams, $field);

            $renderBracket = false;
            if (isset($bracket['render'])) {
                $renderBracket = $bracket['render'];
            }

            foreach ($bracket['params'] as $bracketParam => $bracketValue) {
                if (is_array($bracketValue) && count($bracketValue) > 0) {
                    $renderBracket = true;
                    foreach ($bracketValue as $k => $p) {
                        $returnParams[$k] = $p;
                    }
                }
            }

            ## check if there is another params
            preg_match_all("/\:[\w\d_]+/", $bracket['sql'], $params);
            if (count($params[0]) > 0) {
                if (@$returnParams[$params[0][0]]) {
                    $renderBracket = true;
                }
            }

            if ($renderBracket) {
                $sql = str_replace("{{$block}}", $bracket['sql'], $sql);
            } else {
                $sql = str_replace("{{$block}}", "", $sql);
            }
        }

        if ($sql != "") {
            $sql = DataSource::concatSql($sql, "AND");
            $sql = DataSource::concatSql($sql, "OR");
        }

        return array(
            'sql' => trim($sql),
            'params' => $returnParams
        );
    }

    public function execute($params = array()) {
        $data = $this->evaluate($this->php, true, array('params' => $params));
        $count = count($data);
        $countFunc = 'count($data);';
        if ($this->enablePaging == 'Yes') {
            $count = $this->evaluate($this->pagingPHP, true, array('params' => $params));
            $countFunc = $this->pagingPHP;
        }

        return array(
            'data' => $data,
            'count' => $count,
            'debug' => array(
                'function' => $this->php,
                'count' => $count,
                'countFunction' => $countFunc,
                'params' => $params,
                'timestamp' => date('Y-m-d H:i:s')
            )
        );
    }

    /**
     * @param string $sql parameter query yang akan di-execute
     * @return mixed me-return array kosong jika parameter $sql == "", jika tidak maka akan me-return array data hasil execute SQL
     */
    public function query($params = array()) {
        $params = array_merge($params, $this->queryParams);
        if (trim($this->sql) == "")
            return array();

        $db = Yii::app()->db;
        $template = DataSource::generateTemplate($this->sql, $params, $this);

        ## execute SQL
        $this->command = $db->createCommand($template['sql']);
        $data = $this->command->queryAll(true, $template['params']);

        if ($this->enablePaging == 'Yes') {
            $tc = DataSource::generateTemplate($this->pagingSQL, $params, $this);
            $count = $db->createCommand($tc['sql'])->queryAll(true, $tc['params']);
            if (count($count) > 0) {
                $count = array_values($count[0]);
                $count = $count[0];
            } else {
                $count = 0;
            }
            $template['countSQL'] = $tc['sql'];
        } else {
            $count = count($data);
        }

        $template['count'] = $count;
        $template['timestamp'] = date('Y-m-d H:i:s');

        ## return data
        return array(
            'data' => $data,
            'count' => $count,
            'debug' => $template
        );
    }

    public static function generateCriteria($postedParams, $criteria, $field) {

        ## paging criteria
        if (is_array(@$postedParams['paging'])) {
            $criteria['page'] = $postedParams['paging']['currentPage'];
            $criteria['pageSize'] = $postedParams['paging']['pageSize'];
        }

        if (isset($criteria['order']) && is_string($criteria['order'])) {
            $sql = $criteria['order'];
            $bracket = DataSource::generateTemplate($sql, $postedParams, $field);
            $criteria['order'] = str_replace("order by", "", $bracket['sql']);
        }

        if (isset($criteria['condition']) && is_string($criteria['condition'])) {
            $sql = $criteria['condition'];

            $bracket = DataSource::generateTemplate($sql, $postedParams, $field);
            if ($bracket['sql'] != '') {
                if (substr($bracket['sql'], 0, 5) == 'where') {
                    $criteria['condition'] = substr($bracket['sql'], 5);
                } else {
                    $criteria['condition'] = $bracket['sql'];
                }

                $params = isset($postedParams['params']) ? $postedParams['params'] : array();
                $criteria['params'] = array_merge($params, $bracket['params']);

            } else if ($bracket['sql'] == '') {
                unset($criteria['condition']);
            }
        }

        $criteria['distinct'] = (@$criteria['distinct'] == 'true' ? true : false);

        if (isset($criteria['paging'])) {
            unset($criteria['paging']);
        }

        if (isset($criteria['select']) && $criteria['select'] == '') {
            unset($criteria['select']);
        }

        foreach ($criteria as $k => $m) {
            if (is_string($m)) {
                $criteria[$k] = stripcslashes($m);
            }
        }

        return $criteria;
    }

    public function getRelated($params = array(), $isGenerate = false) {
        $postedParams = array_merge($params, $this->queryParams);
        $relChanges = $this->model->getRelChanges($this->relationTo);

        $criteria = DataSource::generateCriteria($postedParams, $this->relationCriteria, $this);
        if (@$criteria['params']) {
            $criteria['params'] = array_filter($criteria['params']);
        }
        $rawData = $this->model->{$this->relationTo}($criteria);

        if ($this->relationTo == 'currentModel') {
            $tableSchema = $this->model->tableSchema;
            $builder = $this->model->commandBuilder;
            $countCommand = $builder->createCountCommand($tableSchema, new CDbCriteria($criteria));
            $count = $countCommand->queryScalar();
        } else {
            $criteria['select'] = 'count(1) as id';
            $rawCount = $this->model->getRelated($this->relationTo, true, $criteria);
            $count = count($rawCount) > 0 ? $rawCount[0]->id : 0;
        }

        if (count($rawData) == 0 && $isGenerate) {
            if ($this->relationTo != 'currentModel') {
                $rels = $this->model->relations();
                $relClass = $rels[$this->relationTo][1];
            } else {
                $relClass = get_class($this->model);
            }
            $rawData = array($relClass::model()->getAttributes(true, false));
        }

        $data = array(
            'data' => $rawData,
            'debug' => array(
                'count' => $count,
                'params' => $postedParams,
                'debug' => '',
            ),
            'rel' => array(
                'insert_data' => $relChanges['insert'],
                'update_data' => $relChanges['update'],
                'delete_data' => $relChanges['delete'],
            )
        );
        return $data;
    }

    public function processQuery() {
        if (is_string($this->params)) {
            $this->params = array();
        }

        if ($this->relationTo == '' || $this->postData == 'No') {
            ## without relatedTo

            if ($this->fieldType == 'sql') {
                $data = $this->query($this->params);
            } else {
                $data = $this->execute($this->params);
            }
        } else {
            ## with relatedTo
            $data = $this->getRelated($this->params);
        }

        $this->data = array(
            'data' => @$data['data'],
            'count' => @$data['debug']['count'],
            'params' => @$data['debug']['params'],
            'debug' => ($this->debugSql == 'Yes' ? @$data['debug'] : array()),
            'rel' => @$data['rel']
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('data-source.js');
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->processQuery();
        return $this->renderInternal('template_render.php');
    }

}
