<?php

/**
 * Class TextField
 * @author rizky
 */
class TextField extends FormField {

    /**
     * @return array me-return array property TextField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'menuPos' => 'pull-right',
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Field Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'text' => 'Text Field',
                    'password' => 'Password Field',
                ),
                'showOther' => 'Yes',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column3' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column4' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Auto Complete',
                'name' => 'autocomplete',
                'options' => array (
                    'ng-model' => 'active.autocomplete',
                    'ng-change' => 'generateAutoComplete(scope); save();',
                ),
                'listExpr' => '[\'\'=>\'Off\',\'rel\'=>\'On - Using Relation\', \'php\'=>\'On - Using PHP\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Typing Mode',
                'name' => 'acMode',
                'options' => array (
                    'ng-model' => 'active.acMode',
                    'ng-change' => 'save();',
                    'ng-if' => 'active.autocomplete != \'\'',
                ),
                'listExpr' => '[\'\'=>\'Default\',\'comma\'=>\'Comma Separated\']',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"active.autocomplete == \'rel\'\">
<hr/>',
            ),
            array (
                'name' => 'TypeRelation',
                'subForm' => 'application.components.ui.FormFields.TextFieldRelation',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div ng-if=\"active.autocomplete == \'php\'\">
<hr/>',
            ),
            array (
                'label' => 'PHP Expression',
                'fieldname' => 'acPHP',
                'type' => 'ExpressionField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<hr/>',
            ),
            array (
                'column1' => array (
                    array (
                        'name' => 'prefix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Prefix',
                        'options' => array (
                            'ng-model' => 'active.prefix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'name' => 'postfix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Postfix',
                        'options' => array (
                            'ng-model' => 'active.postfix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $fieldType */
    public $fieldType = 'text';

    /** @var string $value */
    public $value = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var string $prefix */
    public $prefix = '';

    /** @var string $postfix */
    public $postfix = '';
    public $autocomplete = '';
    public $acMode = '';

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Text Field";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-text-height";

    /** @var string $name */
    public $modelClass = '';
    public $params = [];
    public $criteria = [
        'select'    => '',
        'distinct'  => 'true',
        'alias'     => 't',
        'condition' => '{[search]}',
        'order'     => '',
        'group'     => '',
        'having'    => '',
        'join'      => ''
    ];
    public $idField = '';
    public $labelField = '';
    public $acPHP = '';
    public $acList = [];

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['text-field.js'];
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
     * dan me-load option label dari property $labelOptions
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
     * @return array me-return array hasil proses expression.
     */
    public function processExpr() {

        if ($this->acPHP != "") {
            if (FormField::$inEditor) {
                $this->acList = '';
                return ['list' => ''];
            }

            ## evaluate expression
            $this->acList = $this->evaluate($this->acPHP, true);

            if (is_array($this->acList) && !Helper::is_assoc($this->acList)) {
                if (!is_array($this->acList[0])) {
                    $this->acList = Helper::toAssoc($this->acList);
                }
            }
        } else if (is_array($this->acList) && !Helper::is_assoc($this->acList)) {
            $this->acList = Helper::toAssoc($this->acList);
        }

        return [
            'list' => $this->acList
        ];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->renderID;
        $this->fieldOptions['name'] = $this->renderName;
        $this->addClass('form-control', 'fieldOptions');

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        if (!is_string($this->value))
            $this->value = json_encode($this->value);

        if ($this->autocomplete == 'php') {
            $this->processExpr();
        }

        return $this->renderInternal('template_render.php');
    }
    
    public function isDisabled() {
        if (isset($this->fieldOptions['ng-disabled'])) {
            return $this->fieldOptions['ng-disabled'];
        }
        if (isset($this->fieldOptions['disabled'])) {
            return "true";
        }
    }
}