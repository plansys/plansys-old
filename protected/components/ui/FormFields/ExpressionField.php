<?php

class ExpressionField extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Active FieldName:',
                'name' => 'fieldname',
                'options' => array (
                    'ng-model' => 'active.fieldname',
                    'ng-change' => 'save()',
                    'ng-form-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array (),
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            '<hr/>',
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Language',
                'name' => 'language',
                'options' => array (
                    'ng-model' => 'active.language',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'html' => 'HTML',
                    'php' => 'PHP',
                    'js' => 'JS',
                    'sql' => 'SQL',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            '<hr/>',
            array (
                'label' => 'When expression change is valid, do this:',
                'fieldname' => 'validAction',
                'validAction' => 'save()',
                'language' => 'js',
                'desc' => 'Example: <br/><span style="font-family:monospace;">active.list = result; </span>',
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Info Message (HTML allowed)',
                'name' => 'desc',
                'labelWidth' => '5',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'options' => array (
                    'ng-model' => 'active.desc',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextArea',
            ),
        );
    }

    public $label = '';
    public $fieldname = '';
    public $validAction = '';
    public $language = 'php';
    public $value = '';
    public $options = array();
    public $desc = '';
    public $labelOptions = array();
    public static $toolbarName = "Expression Field";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-terminal";

    public function includeJS() {
        return array('expression-field.js');
    }

    public function actionValidate() {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata);
        $result = '';

        $this->evaluate(@$post['expr']);
    }
    
    public function getIcon() {
        if ($this->language == "php") return "php";
        if ($this->language == "sql") return "php-database-alt2";
        if ($this->language == "js") return "javascript";
        if ($this->language == "html") return "shell";
    }

    public function render() {
        $this->addClass('field-box');
        if ($this->fieldname != '') {
            $this->options['ng-model'] = 'active.' . $this->fieldname;
        }
        return $this->renderInternal('template_render.php');
    }

}
