<?php

/**
 * Class DataGrid
 * @author rizky
 */
class SubForm extends FormField {

    public $name = '';
    public $mode = 'multi';
    public $subForm = '';
    public $options = [];
    public $value = [];
    public $templateAttributes = [];
    public $inlineJS = '';

    /** @var string $toolbarName */
    public static $toolbarName = "Sub Form";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-file-text-o fa-nm";

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Mode',
                'name' => 'mode',
                'options' => array (
                    'ng-model' => 'active.mode',
                    'ng-change' => 'save()',
                ),
                'defaultType' => 'first',
                'list' => array (
                    'single' => 'Single Field',
                    'multi' => 'SubForm',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SubForm Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.mode == \'multi\'',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    'ng-if' => 'active.mode == \'single\'',
                ),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SubForm',
                'name' => 'subForm',
                'options' => array (
                    'ng-model' => 'active.subForm',
                    'ng-change' => 'save()',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => 'FormBuilder::listForm(null, true)',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Edit SubForm',
                'icon' => 'sign-in',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px;',
                    'href' => 'url:/dev/forms/update?class={active.subForm}',
                    'target' => '_blank',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"clearfix\"></div>
<hr>',
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
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    private $_subformClass = "";

    public function getSubFormClass() {
        if ($this->_subformClass == "") {
            $this->_subformClass = Helper::explodeLast(".", $this->subForm);
        }
        return $this->_subformClass;
    }

    public function getCtrlName() {
        return $this->subFormClass . ucfirst($this->name);
    }

    public function getRenderUrl() {
        return Yii::app()->controller->createUrl('/formfield/SubForm.render', [
                    'name' => $this->name,
                    'mode' => $this->mode,
                    'class' => $this->subForm,
                    'js' => $this->inlineJS
        ]);
    }

    public function includeJS() {
        return ['sub-form.js'];
    }

    public function renderHtml() {
        Yii::import($this->subForm);
        $class = $this->subFormClass;
        if (!class_exists($class)) {
            return '';
        }
        $model = new $class;

        $fb = FormBuilder::load($class);
        $this->templateAttributes = $model->attributes;

        $html = '<div ng-controller="' . $this->ctrlName . 'Controller">';
        $html .= $fb->render(null, [
            'wrapForm' => false
        ]);

        $html .= '</div>';
        return $html;
    }

    public function renderInternalScript() {
        $jspath = explode(".", $this->subForm);
        array_pop($jspath);
        $jspath = implode(".", $jspath);

        $inlineJS = str_replace("/", DIRECTORY_SEPARATOR, trim($this->inlineJS, "/"));
        $inlineJS = Yii::getPathOfAlias($jspath) . DIRECTORY_SEPARATOR . $inlineJS;
        if (is_file($inlineJS)) {
            $inlineJS = file_get_contents($inlineJS);
        } else {
            $inlineJS = '';
        }

        $controller = include("SubForm/controller.js.php");
        return '<script>' . $controller . '</script>';
    }

}