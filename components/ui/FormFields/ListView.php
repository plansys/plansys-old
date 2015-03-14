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
                'label' => 'List Type',
                'name' => 'fieldTemplate',
                'options' => array (
                    'ng-model' => 'active.fieldTemplate',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'default' => 'Single Field',
                    'form' => 'SubForm',
                ),
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Template Form',
                'name' => 'templateForm',
                'options' => array (
                    'ng-model' => 'active.templateForm',
                    'ng-show' => 'active.fieldTemplate == \\\'form\\\'',
                    'ng-change' => 'save();',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => 'FormBuilder::listForm()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\\"active.fieldTemplate == \\\'default\\\'\\">',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\\'margin:10px 0px 10px 10px;border:1px solid #ccc;padding:5px 5px 0px 5px;border-radius:4px;\\\'>',
            ),
            array (
                'label' => 'Field Type',
                'name' => 'singleView',
                'options' => array (
                    'ng-change' => 'activeEditor.fieldTypeChange(active)',
                    'ng-model' => 'active.singleView',
                ),
                'listExpr' => '[\\\'TextField\\\',\\\'DropDownList\\\']',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Edit',
                'icon' => 'pencil',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin-top:-32px;',
                    'ng-click' => 'activeEditor.toggleEdit(active)',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '    <div 
    ng-if=\'active.edited\' 
    class=\'list-view-single-edit\'
    style=\'border-top:1px solid #ccc;margin:0px -5px;padding:5px 5px 0px 5px;\'>',
            ),
            array (
                'name' => 'singleViewOption',
                'mode' => 'single',
                'subForm' => 'application.components.ui.FormFields.TextField',
                'options' => array (
                    'ng-if' => 'active.singleView == \\\'TextField\\\'',
                    'ng-model' => 'active.singleViewOption',
                ),
                'type' => 'SubForm',
            ),
            array (
                'name' => 'singleViewOption',
                'mode' => 'single',
                'subForm' => 'application.components.ui.FormFields.DropDownList',
                'options' => array (
                    'ng-if' => 'active.singleView == \\\'DropDownList\\\'',
                    'ng-model' => 'active.singleViewOption',
                ),
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div><div ng-show=\\"active.fieldTemplate == \\\'form\\\' && active.templateForm != \\\'\\\'\\">',
            ),
            array (
                'label' => 'Edit Subform',
                'icon' => 'sign-in',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px;',
                    'href' => 'url:/dev/forms/update?class={active.templateForm}',
                    'target' => '_blank',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"clearfix\"></div>
<hr/></div>',
            ),
            array (
                'label' => 'Inline JS',
                'name' => 'inlineJS',
                'options' => array (
                    'ng-model' => 'active.inlineJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Sortable',
                'name' => 'sortable',
                'options' => array (
                    'ng-model' => 'active.sortable',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'yes' => 'Yes',
                    'No' => 'No',
                ),
                'fieldWidth' => '5',
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
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
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
    public $sortable = 'yes';
    public $singleView = 'TextField';
    public $singleViewOption = null;

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

    public function includeEditorJS() {
        return ['list-view-editor.js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm flat', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['ui-tree-node'] = '';
        $this->fieldOptions['ng-repeat'] = 'item in value';
        $this->fieldOptions['ng-init'] = 'model = value[$index];';
        $this->addClass('list-view-item', 'fieldOptions');

        Yii::import(FormBuilder::classPath($this->templateForm));
        $class = Helper::explodeLast(".", $this->templateForm);

        if ($this->fieldTemplate == 'form' && class_exists($class)) {
            $fb = FormBuilder::load($class);
            $model = new $class;

            if ($this->value == "") {
                $this->value = [];
            }

            $this->templateAttributes = $model->attributes;
            $this->renderTemplateForm = $fb->render($this->templateAttributes, ['wrapForm' => false]);
        } else if ($this->fieldTemplate == 'default') {
            $field = new $this->singleView;
            $field->attributes = $this->singleViewOption;
            $field->renderID = $this->name . rand(0, 10000);
            $field->builder = $this->builder;
            $field->formProperties = $this->formProperties;

            $this->templateAttributes = ['val' => ''];
            $this->renderTemplateForm = $field->render();
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

        return $this->renderInternal('template_render.php', ['inlineJS' => $inlineJS]);
    }

}