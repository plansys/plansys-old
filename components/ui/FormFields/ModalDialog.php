<?php

/**
 * Class DataGrid
 * @author rizky
 */
class ModalDialog extends FormField {

    public $name = '';
    public $subForm = '';
    public $options = array();
    public $inlineJS = '';
    public $size = '';

    /** @var string $toolbarName */
    public static $toolbarName = "Modal Dialog";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-square fa-nm";

    public function getFieldProperties() {
        return array(
            array(
                'label' => 'SubForm Name',
                'name' => 'name',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'SubForm',
                'name' => 'subForm',
                'options' => array(
                    'ng-model' => 'active.subForm',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'FormBuilder::listForm(null, true)',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Inline JS',
                'name' => 'inlineJS',
                'options' => array(
                    'ng-model' => 'active.inlineJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    private $_subformClass = "";

    public function getSubFormClass() {
        if ($this->_subformClass == "") {
            $this->_subformClass = array_pop(explode(".", $this->subForm));
        }
        return $this->_subformClass;
    }

    public function getCtrlName() {
        return $this->subFormClass . ucfirst($this->name);
    }

    private function renderController() {
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

        $controller = include("ModalDialog/controller.js.php");
        return '<script>' . $controller . '</script>';
    }

    public function includeJS() {
        return array('modal-dialog.js');
    }

    public function renderSubForm() {
        if ($this->subFormClass == get_class($this)) {
            return '<center><i class="fa fa-warning"></i> Error Rendering SubForm: Subform can not be the same as its parent</center>';
        } else {
            ## render
            Yii::import($this->subForm);
            $fb = FormBuilder::load($this->subFormClass);
            $render = $fb->render($fb->model, array(
                'wrapForm' => false
            ));

            return $render;
        }
    }

}
