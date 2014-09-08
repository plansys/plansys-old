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
        return array (
            array (
                'label' => 'Data Source Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Post Data ?',
                'name' => 'postData',
                'options' => array (
                    'ng-model' => 'active.postData',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Debug SQL ?',
                'name' => 'debugSql',
                'options' => array (
                    'ng-model' => 'active.debugSql',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Source Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save()',
                ),
                'list' => array (
                    'sql' => 'SQL',
                    'php' => 'PHP Function',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Paging',
                'name' => 'enablePaging',
                'options' => array (
                    'ng-model' => 'active.enablePaging',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SQL',
                'fieldname' => 'sql',
                'language' => 'sql',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'sql\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'PHP Function',
                'fieldname' => 'php',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'php\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Total Item - PHP Function',
                'fieldname' => 'pagingPHP',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'php\' && active.enablePaging == \'Yes\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Total Item - SQL',
                'fieldname' => 'pagingSQL',
                'language' => 'sql',
                'options' => array (
                    'ng-show' => 'active.fieldType == \'sql\' && active.enablePaging == \'Yes\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Parameters',
                'fieldname' => 'params',
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

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";

    public function actionQuery() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $class = array_pop(explode(".", $post['class']));
        Yii::import($post['class']);

        if (class_exists($class)) {
            $fb = FormBuilder::load($class);
            $field = $fb->findField(array('name' => $post['name']));
            $params = @$post['params'];

            $this->attributes = $field;
            $this->builder = $fb;

            if ($this->fieldType == 'sql') {
                $data = $this->query($params);
            } else {
                $data = $this->execute($params);
            }

            echo json_encode(array(
                'data' => $data['data'],
                'debug' => ($this->debugSql == 'Yes' ? $data['debug'] : array())
            ));
        }
    }

    protected function processSQLBracket($sql, $postedParams) {
        preg_match_all("/\[(.*?)\]/", $sql, $matches);
        $params = $matches[1];
        $parsed = array();

        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                $sql = str_replace("[{$param}]", "", $sql);
                $parsed[$param] = "";
                continue;
            }

            $field = $this->builder->findField(array('name' => $this->params[$param]));

            if ($field['type'] == "DataFilter") {
                $fieldSql = 'DataFilter::generateParams($paramName, $params)';
            } else if ($field['type'] == "DataGrid") {
                $fieldSql = 'DataGrid::generateParams($paramName, $params)';
            } else {
                $fieldSql = @$field['options']['ps-ds-sql'];
            }

            if (isset($fieldSql)) {
                $template = $this->evaluate($fieldSql, true, array(
                    'paramName' => $param,
                    'params' => @$postedParams[$param]
                ));

                $sql = str_replace("[{$param}]", $template['sql'], $sql);
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

    public function generateTemplate($sql, $postedParams = array()) {

        preg_match_all("/\{(.*?)\}/", $sql, $blocks);
        $returnParams = array();

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
     * @return mixed me-return arraykosong jika parameter $sql == "", jika tidak maka akan me-return array data hasil execute SQL
     */
    public function query($params = array()) {
        if (trim($this->sql) == "")
            return array();

        $db = Yii::app()->db;
        $template = $this->generateTemplate($this->sql, $params);

        ## execute SQL
        $data = $db->createCommand($template['sql'])->queryAll(true, $template['params']);

        if ($this->enablePaging == 'Yes') {
            $tc = $this->generateTemplate($this->pagingSQL, $params);
            $count = $db->createCommand($tc['sql'])->queryAll(true, $tc['params']);
            $count = array_values($count[0]);
            $count = $count[0];
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

    /**
     * @return array me-return array hasil proses expression.
     */
    public function processExpr() {
        if (!FormField::$inEditor) {
            if ($this->fieldType == 'sql') {
                $this->data = $this->query();
            } else {
                $this->data = $this->execute();
            }
        }

        return array(
            'data' => $this->data
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
        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
