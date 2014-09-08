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
                'fieldWidth' => '7',
                'type' => 'DropDownList',
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
                $data = array(
                    'data' => $this->evaluate($this->php, true, array('params' => $params)),
                    'debug' => ''
                );
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
            foreach ($bracket['params'] as $bracketParam => $bracketValue) {
                if (count($bracketValue) > 0) {
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
            'sql' => $sql,
            'params' => $returnParams
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

        ## return data
        return array(
            'data' => $data,
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
                $this->data = array(
                    'data' => $this->evaluate($this->php, true),
                    'debug' => ''
                );
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
