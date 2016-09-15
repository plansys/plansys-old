<?php

/**
 * Class DropDownList
 * @author rizky
 */
class DropDownList extends FormField {

    /**
     * @return array me-return array property DropDown.
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
                    'ng-delay' => 500,
                ),
                'type' => 'TextField',
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
                'label' => 'Menu Position',
                'name' => 'menuPos',
                'options' => array (
                    'ng-model' => 'active.menuPos',
                    'ng-change' => 'save();',
                ),
                'listExpr' => '[\'\'=>\'Left\',\'pull-right\'=>\'Right\']',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'totalColumns' => '4',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => 500,
                            'ng-disabled' => 'active.layout == \'Vertical\'',
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
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => 500,
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '25%',
                'w2' => '25%',
                'w3' => '25%',
                'w4' => '25%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Searchable',
                'name' => 'searchable',
                'options' => array (
                    'ng-model' => 'active.searchable',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show \'Other\' Item',
                'name' => 'showOther',
                'options' => array (
                    'ng-model' => 'active.showOther',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => '\'Other\' Item Label',
                'name' => 'otherLabel',
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'options' => array (
                    'ng-model' => 'active.otherLabel',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-if' => 'active.showOther == \'Yes\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Default Value',
                'name' => 'defaultType',
                'options' => array (
                    'ng-model' => 'active.defaultType',
                    'ng-change' => 'save()',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => '[\'\'=>\'-- None --\',\'first\' => \'First Item\']',
                'labelWidth' => '6',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'DropDown Item',
                'name' => 'list',
                'show' => 'Show',
                'options' => array (
                    'ng-hide' => '(typeof(active.listExpr) != \'undefined\' && active.listExpr != \'\') || active.options[\'ps-list\'] != null',
                ),
                'allowEmptyKey' => 'Yes',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => array (
                    'ng-hide' => 'active.options[\'ps-list\'] != null',
                    'ps-valid' => 'active.list = result;save();',
                ),
                'desc' => '<i class=\'fa fa-warning\'></i> WARNING: Using List Expression will replace <i>DropDown Item</i> with expression result',
                'type' => 'ExpressionField',
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

    /** @var string $value digunakan pada function checked */
    public $value = '';

    /** @var array $options */
    public $options = [];
    public $menuPos = '';
    public $defaultType  = '';
    public $defaultValue = '';

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var string $list */
    public $list = '';

    /** @var string $listExpr digunakan pada function processExpr */
    public $listExpr = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var string $searchable */
    public $searchable = 'No';

    /** @var string $showOther */
    public $showOther = 'No';

    /** @var string $otherLabel */
    public $otherLabel = 'Lainnya';

    /** @var string $toolbarName */
    public static $toolbarName = "Drop Down List";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-caret-square-o-down";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['drop-down-list.js'];
    }

    /**
     * @return array me-return array hasil proses expression.
     */
    public function processExpr() {
        
        if ($this->listExpr != "") {
            if (FormField::$inEditor) {
                $this->list = '';
                return ['list' => ''];
            }
        
            ## evaluate expression
            $this->list = $this->evaluate($this->listExpr, true);

            if (is_array($this->list) && !Helper::is_assoc($this->list)) {
                if (!is_array($this->list[0])) {
                    $this->list = Helper::toAssoc($this->list);
                }
            }
        } 
        
        return [
            'list' => $this->list
        ];
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

        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);

        $this->processExpr();
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