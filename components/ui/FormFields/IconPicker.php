<?php

/**
 * Class IconPicker
 * @author rizky
 */
class IconPicker extends FormField {

    /**
     * @return array me-return array property IconPicker.
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
                'type' => 'Text',
                'value' => '<hr/>',
            ],
             [
                'label' => 'Label Width',
                'name' => 'labelWidth',
                'fieldWidth' => '3',
                'options' =>  [
                    'ng-model' => 'active.labelWidth',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-disabled' => 'active.layout == \\\'Vertical\\\';',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Box Width',
                'name' => 'fieldWidth',
                'fieldWidth' => '4',
                'postfix' => 'px',
                'options' =>  [
                    'ng-model' => 'active.fieldWidth',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'type' => 'Text',
                'value' => '<hr/>',
            ],
             [
                'label' => 'Render Empty',
                'name' => 'renderEmpty',
                'options' =>  [
                    'ng-model' => 'active.renderEmpty',
                    'ng-change' => 'save();',
                ],
                'list' =>  [
                    'Yes' => 'Yes',
                    'No' => 'No',
                ],
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Icon Template',
                'fieldname' => 'iconTemplate',
                'language' => 'html',
                'options' =>  [
                    'ps-valid' => 'save()',
                ],
                'type' => 'ExpressionField',
            ],
             [
                'label' => 'Icon List',
                'name' => 'list',
                'options' =>  [
                    'ng-show' => 'active.listExpr == \\\'\\\'',
                    'ng-model' => 'active.list',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' =>  [
                    'ng-hide' => 'active.options[\\\'ps-list\\\'] != null',
                    'ps-valid' => 'save()',
                ],
                'desc' => 'WARNING: Using List Expression will replace <i>Radio Button Item</i> with expression result',
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
        ];
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $value digunakan pada function getIcon */
    public $value = '';

    /** @var string $list */
    public $list = '';

    /** @var string $listExpr */
    public $listExpr = '';

    /** @var string $renderEmpty */
    public $renderEmpty = "No";

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var string $iconTemplate */
    public $iconTemplate = '<i class="fa fa-fw fa-lg {icon}"></i>';

    /** @var string $fieldWidth */
    public $fieldWidth = "265";

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Icon Picker";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-smile-o";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['icon-picker.js'];
    }

    /**
     * @param string $value
     * @return mixed me-return array atau string hasil dari str_replace
     */
    public function getIcon($value = null) {
        if (is_null($value)) {
            $value = $this->value;
        }
        
        $template = stripcslashes($this->iconTemplate);
        $template = str_replace("{base_url}", Yii::app()->baseUrl, $template);
        $template = str_replace("{app_url}", Yii::app()->baseUrl . '/' . Setting::get('app.dir'), $template);
        $template = str_replace("{plansys_url}", Yii::app()->baseUrl . '/' . Setting::getPlansysDirName(), $template);

        if ($this->renderEmpty == "Yes") {
            return str_replace("{icon}", $value, $template);
        } else {
            return ($value == "" ? "" : str_replace("{icon}", $value, $template));
        }
    }

    /**
     * @return array me-return array list hasil proses expression.
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
        return "col-sm-" . ($this->layout == 'Vertical' ? 12 : 12 - $this->labelWidth);
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
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field iconPicker dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sml', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}