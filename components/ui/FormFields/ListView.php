<?php

/**
 * Class CheckboxList
 * @author rizky
 */
class ListView extends FormField {

    /**
     * @return array me-return array property TextField.
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
                'label' => 'Field Template',
                'name' => 'fieldTemplate',
                'options' =>  [
                    'ng-model' => 'active.fieldTemplate',
                    'ng-change' => 'save();',
                ],
                'list' =>  [
                    'default' => 'Default',
                    'form' => 'Form',
                ],
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Template Form',
                'name' => 'templateForm',
                'options' =>  [
                    'ng-model' => 'active.templateForm',
                    'ng-show' => 'active.fieldTemplate == \\\'form\\\'',
                    'ng-change' => 'save();',
                ],
                'listExpr' => 'FormBuilder::listForm()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Inline JS',
                'name' => 'inlineJS',
                'options' =>  [
                    'ng-model' => 'active.inlineJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'value' => '<div ng-show=\"active.fieldTemplate == \'form\'\" class=\"well well-sm\">
Use this code to access current item: <br/> 
<code>ng-model = value[$index]</code><br/>
    <code> ng-change = updateListView() </code> 
</div>',
                'type' => 'Text',
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
                ],
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
             [
                'column1' =>  [
                     [
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' =>  [
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \\\'Vertical\\\'',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ],
                ],
                'column2' =>  [
                     [
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' =>  [
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ],
                        'type' => 'TextField',
                    ],
                     [
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ],
                ],
                'column3' =>  [
                     [
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ],
                ],
                'column4' =>  [
                     [
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ],
                ],
                'type' => 'ColumnField',
            ],
             [
                'value' => '<hr/>',
                'type' => 'Text',
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

    /** @var string $fieldTemplate */
    public $fieldTemplate = 'default';

    /** @var string $templateForm */
    public $templateForm = '';

    /** @var string $value */
    public $value = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var string $layoutVertical */
    public $layoutVertical = '';

    /** @var integer $labelWidth */
    public $labelWidth = 4;
    
    public $inlineJS = '';

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "List View";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-align-justify";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['list-view.js'];
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

    protected $renderTemplateForm;
    protected $templateAttributes = [];

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->name;
        $this->fieldOptions['name'] = $this->name;
        $this->addClass('form-control', 'fieldOptions');

        Yii::import(FormBuilder::classPath($this->templateForm));
        $class = array_pop(explode(".", $this->templateForm));


        if ($this->fieldTemplate == "form" && class_exists($class)) {
            $fb = FormBuilder::load($class);
            $model = new $class;
            
            if ($this->value == "") {
                $this->value = [];
            }
            
            $this->templateAttributes = $model->attributes;
            $this->renderTemplateForm = $fb->render($this->templateAttributes, ['wrapForm' => false]);
        }
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        
        $jspath = explode(".", FormBuilder::classPath($this->templateForm));
        array_pop($jspath);
        $jspath = implode(".", $jspath);
        
        $inlineJS = str_replace("/", DIRECTORY_SEPARATOR, trim($this->inlineJS, "/"));
        $inlineJS = Yii::getPathOfAlias($jspath) . DIRECTORY_SEPARATOR . $inlineJS;
        
        if (is_file($inlineJS)) {
            $inlineJS = file_get_contents($inlineJS);
        } else {
            $inlineJS = '';
        }
        
        return $this->renderInternal('template_render.php', ['inlineJS'=>$inlineJS]);
    }

}