<?php

/**
 * Class DataSource
 * @author rizky
 */
class DataSource extends FormField {

    /**
     * @return array Fungsi ini akan me-return array property HiddenField.
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
                'label' => 'Source Type',
                'name' => 'fieldType',
                'options' => array(
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'sql' => 'SQL',
                    'php' => 'PHP Function',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'SQL',
                'fieldname' => 'sql',
                'language' => 'sql',
                'options' => array(
                    'ng-show' => 'active.fieldType == \'sql\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'PHP Function',
                'fieldname' => 'php',
                'options' => array(
                    'ng-show' => 'active.fieldType == \'php\'',
                    'ps-valid' => 'save();',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Parameters',
                'fieldname' => 'params',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $name */
    public $name = '';
    public $fieldType = 'sql';
    public $sql = '';
    public $php = '';
    public $params = '';
    public $data;

    /** @var boolean $isHidden */
    public $isHidden = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Data Source";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-book";

    public function query($sql) {
        if (trim($sql) == "")
            return "";

        $db = Yii::app()->db;

        ## bind params
        $params = array();

        ## execute SQL
        $data = $db->createCommand($sql)->queryAll(true, $params);

        ## return data
        return $data;
    }

    public function processExpr() {
        if ($this->fieldType == 'sql') {
            $this->data = $this->query($this->sql);
        } else {
            $this->data = $this->evaluate($this->php, true);
        }

        return array(
            'data' => $this->data
        );
    }

    public function includeJS() {
        return array('data-source.js');
    }

    public function render() {
        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
