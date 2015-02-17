<?php

/**
 * Class RadioButtonList
 * @author rizky
 */
class RadioButtonList extends FormField {

    /**
     * @return array me-return array property RadioButton.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Field Name',
                'name' => 'name',
                'options' =>  [
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ],
                'list' =>  [],
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Label',
                'name' => 'label',
                'options' =>  [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Layout',
                'name' => 'layout',
                'options' =>  [
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Item Layout',
                'name' => 'itemLayout',
                'options' =>  [
                    'ng-model' => 'active.itemLayout',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\',\\\'ButtonGroup\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Label Width',
                'name' => 'labelWidth',
                'fieldWidth' => '4',
                'options' =>  [
                    'ng-model' => 'active.labelWidth',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-disabled' => 'active.layout == \\\'Vertical\\\';',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Radio Button Item',
                'name' => 'list',
                'options' =>  [
                    'ng-hide' => 'active.listExpr != \\\'\\\' || active.options[\\\'ps-list\\\'] != null',
                ],
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' =>  [
                    'ng-hide' => 'active.options[\\\'ps-list\\\'] != null',
                    'ps-valid' => 'active.list = result;save();',
                ],
                'desc' => '<i class=\\"fa fa-warning\\"></i> WARNING: Using List Expression will replace <i>Radio Button Item</i> with expression result',
                'type' => 'ExpressionField',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $value digunakan pada function checked */
    public $value = '';

    /** @var string $list */
    public $list = '';

    /** @var string $listExpr digunakan pada function processExpr */
    public $listExpr = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var string $itemLayout */
    public $itemLayout = 'Vertical';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "RadioButton List";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-dot-circle-o";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['radio-button-list.js'];
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

            ## change sequential array to associative array
            if (is_array($this->list) && !Helper::is_assoc($this->list)) {
                $this->list = Helper::toAssoc($this->list);
            }
        } else if (is_array($this->list) && !Helper::is_assoc($this->list)) {
            $this->list = Helper::toAssoc($this->list);
        }

        return [
            'list' => $this->list
        ];
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
     * checked
     * Fungsi ini untuk mengecek value dari field ada dalam sebuah array list
     * @param string $value
     * @return string me-return string hasil checked
     */
    public function checked($value) {
        return $value == $this->value ? 'checked="checked"' : '';
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . ($this->layout == 'Vertical' ? 12 : 12 - $this->labelWidth);
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut RadioButtonList dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm');
        $this->addClass($this->layoutClass);
        $this->addClass($this->errorClass);

        $this->addClass('input-group', 'fieldOptions');
        if ($this->itemLayout == "Horizontal") {
            $this->addClass('inline', 'fieldOptions');
        }

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}