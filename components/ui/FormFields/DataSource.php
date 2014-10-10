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
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
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
                'fieldname' => 'params',
                'show' => 'Show',
                'options' => array(
                    'ng-if' => 'active.relationTo == \\\'\\\' || active.postData == \\\'No\\\'',
                ),
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
    public $postData = 'No';

    /** @var string $params */
    public $params = '';
    private $postedParams = '';

    /** @var string $data */
    public $data;
    public $debugSql = 'No';
    public $enablePaging = 'No';
    public $pagingSQL = '';
    public $pagingPHP = '';
    public $relationTo = '';
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
            $this->queryParams = @$post['params'];
            $this->attributes = $field;
            $this->builder = $fb;

            $isGenerate = isset($post['generate']);

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

    protected function processSQLBracket($sql, $postedParams) {
        preg_match_all("/\[(.*?)\]/", $sql, $matches);
        $params = $matches[1];
        $parsed = array();


        foreach ($params as $param) {
            $template = $sql;
            if (!isset($this->params[$param])) {
                $sql = str_replace("[{$param}]", "", $sql);
                $parsed[$param] = "";
                continue;
            }

            $field = $this->builder->findField(array('name' => $this->params[$param]));

            if ($field['type'] == "DataFilter") {
                $fieldSql = 'DataFilter::generateParams($paramName, $params, $template)';
            } else if ($field['type'] == "DataGrid") {
                $fieldSql = 'DataGrid::generateParams($paramName, $params, $template)';
            } else {
                $fieldSql = @$field['options']['ps-ds-sql'];
            }

            if (isset($fieldSql)) {
                $template = $this->evaluate($fieldSql, true, array(
                    'paramName' => $param,
                    'params' => @$postedParams[$param],
                    'template' => $template
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
        if ($this->relationTo == '') {
            return $this->name;
        } else {
            $name = str_replace($this->name, $this->relationTo, $this->renderName);

            if ($mode != '') {
                $name = substr_replace($name, $mode . ']', -1);
            }
            return $name;
        }
    }

    public function generateTemplate($sql, $postedParams = array()) {
        $returnParams = array();

        ## find all blocks
        preg_match_all("/\{(.*?)\}/", $sql, $blocks);

        foreach ($blocks[1] as $block) {
            $bracket = $this->processSQLBracket($block, $postedParams);

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

            if ($renderBracket) {
                $sql = str_replace("{{$block}}", $bracket['sql'], $sql);
            } else {
                $sql = str_replace("{{$block}}", "", $sql);
            }
        }

        ## find all params
        preg_match_all("/\:[\w\d_]+/", $sql, $params);
        $model = $this->model;
        foreach ($params[0] as $p) {
            if (isset($postedParams[$p])) {
                if (strpos($postedParams[$p], 'js:') !== false) {
                    $returnParams[$p] = @$this->queryParams[$p];
                } else {
                    $returnParams[$p] = $this->evaluate($postedParams[$p], true);
                }
            }
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
        if (trim($this->sql) == "")
            return array();


        $db = Yii::app()->db;
        $template = $this->generateTemplate($this->sql, $params);

        ## execute SQL
        $this->command = $db->createCommand($template['sql']);
        $data = $this->command->queryAll(true, $template['params']);

        if ($this->enablePaging == 'Yes') {
            $tc = $this->generateTemplate($this->pagingSQL, $params);
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

    public function getRelated($params = array(), $isGenerate = false) {
        $postedParams = $this->queryParams;
        $criteria = array();

        if (is_array(@$postedParams['paging'])) {
            $criteria['page'] = $postedParams['paging']['currentPage'];
            $criteria['pageSize'] = $postedParams['paging']['pageSize'];
        }

        if (is_array(@$postedParams['order']) && count(@$postedParams['where']) > 0) {
            $sql = '[order]';
            $bracket = $this->processSQLBracket($sql, $postedParams);
            $criteria['order'] = str_replace("order by", "", $bracket['sql']);
        }

        if (is_array(@$postedParams['where']) && count(@$postedParams['where']) > 0) {
            $sql = '[where]';
            $bracket = $this->processSQLBracket($sql, $postedParams);
            $criteria['condition'] = substr($bracket['sql'], 5);
            $criteria['params'] = $bracket['params']['where'];
        }

        $relChanges = $this->model->getRelChanges($this->relationTo);

        if ($this->relationTo != 'currentModel') {
            $rawData = $this->model->{$this->relationTo}($criteria);
            $count = $this->model->{$this->relationTo . "Count"};

            if (count($rawData) == 0 && $isGenerate) {
                $rels = $this->model->relations();
                $relClass = $rels[$this->relationTo][1];
                $rawData = array($relClass::model()->getAttributes(true, false));
            }
        } else {

            if (isset($criteria['page'])) {
                $criteria['offset'] = ($criteria['page'] - 1) * $criteria['pageSize'];
                $criteria['limit'] = $criteria['pageSize'];
                unset($criteria['page'], $criteria['pageSize']);
            } else {
                $criteria['limit'] = 25;
            }

            $tableSchema = $this->model->tableSchema;
            $builder =  $this->model->commandBuilder;
            
            ## find
            $command = $builder->createFindCommand($tableSchema, new CDbCriteria($criteria));
            $rawData = $command->select('*')->queryAll();

            ## count
            $countCommand = $builder->createCountCommand($tableSchema, new CDbCriteria($criteria));
            $count = $countCommand->queryScalar();

            if (count($rawData) == 0 && $isGenerate) {
                $rawData = array($this->model->getAttributes(true, false));
            }
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
        if ($this->relationTo == '' || $this->postData == 'No') {
            ## without relatedTo

            if ($this->fieldType == 'sql') {
                $data = $this->query($this->params);
            } else {
                $data = $this->execute($this->params);
            }
        } else {
            ## with relatedTo
            $data = $this->getRelated();
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
