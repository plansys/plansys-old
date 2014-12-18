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
                'searchable' => 'Yes',
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
                    'phpsql' => 'PHP (Return SQL)',
                    'php' => 'PHP (Return Array)',
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
                    'ng-show' => '(active.fieldType == \\\'php\\\' || active.fieldType == \\\'phpsql\\\') && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'desc' => 'ex: Model::yourFunction($params);',
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Total Item - PHP Function',
                'fieldname' => 'pagingPHP',
                'options' => array(
                    'ng-show' => '(active.fieldType == \\\'php\\\' || active.fieldType == \\\'phpsql\\\') && active.enablePaging == \\\'Yes\\\' && (active.relationTo == \\\'\\\' || active.postData == \\\'No\\\')',
                    'ps-valid' => 'save();',
                ),
                'desc' => 'ex: Model::yourFunction($params);',
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
    public $params = [];
    private $postedParams = [];

    /** @var string $data */
    public $data;
    public $debugSql = 'No';
    public $enablePaging = 'No';
    public $pagingSQL = '';
    public $pagingPHP = '';
    public $relationTo = '';
    public $relationCriteria = [
        'select' => '',
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[where]}',
        'order' => '{[order]}',
        'paging' => '{[paging]}',
        'group' => '',
        'having' => '',
        'join' => ''
    ];
    private $command;

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";
    private $queryParams = [];

    public function actionRelClass() {
        Yii::import($_GET['class']);
        $class = Helper::explodeLast(".", $_GET['class']);

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
        $class = Helper::explodeLast(".", $post['class']);
        Yii::import($post['class']);

        if (class_exists($class)) {
            $fb = FormBuilder::load($class);
            $field = $fb->findField(['name' => $post['name']]);

            $this->queryParams = (is_array(@$post['params']) ? @$post['params'] : []);

            if ($field['fieldType'] != "php" && method_exists($class, 'model')) {
                $fb->model = $class::model()->findByPk(@$post['model_id']);
                if (is_null($fb->model)) {
                    $fb->model = new $class;
                }
            }

            $this->attributes = $field;
            $this->builder = $fb;

            $isGenerate = isset($post['generate']);

            if (is_string($this->params)) {
                $this->params = [];
            }

            if ($this->postData == 'No' || $this->relationTo == '' || $this->relationTo == '-- NONE --') {
                ## without relatedTo

                switch ($this->fieldType) {
                    case "sql":
                        $data = $this->query($this->params);
                        break;
                    case "phpsql":
                        $this->sql = $this->execute($this->params);
                        $data = $this->query($this->params);
                        break;
                    case "php":
                        $data = $this->execute($this->params);
                        break;
                }
            } else {
                ## with relatedTo

                $data = $this->getRelated($this->params, $isGenerate);
            }

            echo json_encode([
                'data' => $data['data'],
                'count' => $data['debug']['count'],
                'params' => $data['debug']['params'],
                'debug' => ($this->debugSql == 'Yes' ? $data['debug'] : [])
            ]);
        }
    }

    protected static function processSQLBracket($sql, $postedParams, $field) {
        preg_match_all("/\[(.*?)\]/", $sql, $matches);
        $params = $matches[1];
        $parsed = [];


        foreach ($params as $param) {
            $template = $sql;
            $paramOptions = explode("|", $param);
            $param = array_shift($paramOptions);

            if (!isset($field->params[$param]) && !isset($field->queryParams[$param])) {
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
                    $ff = $field->builder->findField(['name' => $field->params[$param]]);
                    $fieldSql = @$ff['options']['ps-ds-sql'];
                    break;
            }

            if (isset($fieldSql)) {
                $template = $field->evaluate($fieldSql, true, [
                    'paramName' => $param,
                    'params' => @$postedParams[$param],
                    'template' => $template,
                    'paramOptions' => $paramOptions
                ]);



                if (!isset($template['generateTemplate'])) {
                    $sql = str_replace("[{$param}]", $template['sql'], $sql);
                } else {
                    $sql = $template['sql'];
                }

                if ($template['sql'] != '') {
                    $parsed[$param] = $template['params'];
                }

                if (isset($template['render'])) {
                    return ['sql' => $sql, 'params' => $parsed, 'render' => $template['render']];
                }
            }
        }
        return ['sql' => $sql, 'params' => $parsed];
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

    public static function generateTemplate($sql, $postedParams = [], $field, $paramDefs = []) {
        $returnParams = [];

        ## find all params
        preg_match_all("/\:[\w\d_]+/", $sql, $params);
        $model = $field->model;
        foreach ($params[0] as $p) {
            if (isset($postedParams[$p])) {
                $isJs = strpos($postedParams[$p], 'js:') !== false || (isset($paramDefs[$p]) && strpos($paramDefs[$p], 'js:') !== false);

                if ($isJs) {
                    switch (get_class($field)) {
                        case "DataSource":
                            $returnParams[$p] = @$field->queryParams[$p];
                            break;
                        default:
                            $returnParams[$p] = '';
                            break;
                    }
                } else {
                    $returnParams[$p] = $field->evaluate($postedParams[$p], true, [
                        'model' => $model
                    ]);
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

        return [
            'sql' => trim($sql),
            'params' => $returnParams
        ];
    }

    public function execute($params = []) {
        $params = array_merge($params, $this->queryParams);

        $data = $this->evaluate($this->php, true, ['params' => $params]);

        $count = count($data);
        $countFunc = 'count($data);';
        if ($this->enablePaging == 'Yes') {
            $count = $this->evaluate($this->pagingPHP, true, ['params' => $params]);
            $countFunc = $this->pagingPHP;
        }

        if ($this->fieldType == "php") {
            return [
                'data' => $data,
                'count' => $count,
                'debug' => [
                    'function' => $this->php,
                    'count' => $count,
                    'countFunction' => $countFunc,
                    'params' => $params,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return $data;
        }
    }

    /**
     * @param string $sql parameter query yang akan di-execute
     * @return mixed me-return array kosong jika parameter $sql == "", jika tidak maka akan me-return array data hasil execute SQL
     */
    public function query($params = []) {
        $paramDefs = $params;
        $params = array_merge($params, $this->queryParams);
        if (trim($this->sql) == "")
            return [];

        $db = Yii::app()->db;
        $template = DataSource::generateTemplate($this->sql, $params, $this, $paramDefs);
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
        return [
            'data' => $data,
            'count' => $count,
            'debug' => $template
        ];
    }

    public static function generateCriteria($postedParams, $criteria, $field) {

        ## paging criteria
        if (is_array(@$postedParams['paging'])) {
            if (isset($postedParams['paging']['currentPage'])) {
                $criteria['page'] = $postedParams['paging']['currentPage'];
            } else {
                $criteria['page'] = 1;
            }
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

                $params = isset($postedParams['params']) ? $postedParams['params'] : [];
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

    public function getRelated($params = [], $isGenerate = false) {
        $postedParams = array_merge($params, $this->queryParams);
        $relChanges = $this->model->getRelChanges($this->relationTo);

        $criteria = DataSource::generateCriteria($postedParams, $this->relationCriteria, $this);
        if (@$criteria['params']) {
            $criteria['params'] = array_filter($criteria['params']);
        }

        $criteriaCount = $criteria;
        if ($this->relationTo == 'currentModel') {
            $tableSchema = $this->model->tableSchema;
            $builder = $this->model->tableSchema;
            $builder = $this->model->commandBuilder;
            if (array_key_exists('page', $criteriaCount)) {
                $start = ($criteriaCount['page'] - 1) * $criteriaCount['pageSize'];
                $pageSize = $criteriaCount['pageSize'];
                $criteriaCount['limit'] = $pageSize;
                $criteriaCount['offset'] = $start;

                unset($criteriaCount['pageSize']);
                unset($criteriaCount['page']);
            }

            $countCommand = $builder->createCountCommand($tableSchema, new CDbCriteria($criteriaCount));
            $count = $countCommand->queryScalar();
        } else {
            $criteriaCount = $criteria;
            $criteriaCount['select'] = 'count(1) as id';
            $rawCount = $this->model->getRelated($this->relationTo, true, $criteriaCount);
            if (!is_array($rawCount)) {
                throw New Exception('Relation defintion is wrong! check your relations() function in model');
            }

            $count = count($rawCount) > 0 ? $rawCount[0]->id : 0;
        }

        $rawData = $this->model->{$this->relationTo}($criteria, false);

        if (count($rawData) == 0 && $isGenerate) {
            if ($this->relationTo != 'currentModel') {
                $rels = $this->model->relations();
                $relClass = $rels[$this->relationTo][1];
            } else {
                $relClass = get_class($this->model);
            }

            $rawData = [$relClass::model()->getAttributes(true, false)];
        }

        $data = [
            'data' => $rawData,
            'debug' => [
                'count' => $count,
                'params' => $postedParams,
                'debug' => '',
            ],
            'rel' => [
                'insert_data' => $relChanges['insert'],
                'update_data' => $relChanges['update'],
                'delete_data' => $relChanges['delete'],
            ]
        ];
        return $data;
    }

    public function processQuery() {
        if (is_string($this->params)) {
            $this->params = [];
        }


        if ($this->relationTo == '' || $this->postData == 'No') {
            ## without relatedTo

            switch ($this->fieldType) {
                case "sql":
                    $data = $this->query($this->params);
                    break;
                case "phpsql":
                    $this->sql = $this->execute($this->params);
                    $data = $this->query($this->params);
                    break;
                case "php":
                    $data = $this->execute($this->params);
                    break;
            }
        } else {
            ## with relatedTo
            $data = $this->getRelated($this->params);
        }

        $this->data = [
            'data' => @$data['data'],
            'count' => @$data['debug']['count'],
            'params' => @$data['debug']['params'],
            'debug' => ($this->debugSql == 'Yes' ? @$data['debug'] : []),
            'rel' => @$data['rel']
        ];
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['data-source.js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {

        $execQuery = true;
        if (isset($this->params['where'])) {
            $field = $this->builder->findField(['name' => $this->params['where']]);
            if ($field) {
                foreach ($field['filters']as $f) {
                    $dateCondition = @$f['defaultOperator'] != '' && @$f['filterType'] == 'date';

                    if (@$f['defaultValue'] != '' || $dateCondition ||
                            @$f['defaultValueFrom'] != '' || @$f['defaultValueTo'] != ''
                    ) {
                        var_dump($f);
                        $execQuery = false;
                    }
                }
            }
        }
        
        if ($execQuery) {
            $this->processQuery();
        } else {
            $this->data = [
                'data' => [],
                'count' => 0,
                'params' => $this->params,
                'debug' => '',
                'rel' => ''
            ];
        }

        return $this->renderInternal('template_render.php');
    }

}
