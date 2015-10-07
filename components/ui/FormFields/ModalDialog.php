<?php

/**
 * Class DataGrid
 * @author rizky
 */
class ModalDialog extends FormField {

    public static $toolbarName   = "Modal Dialog";
    public static $category      = "Layout";
    public static $toolbarIcon   = "fa fa-square fa-nm";
    public static $deprecated    = true;
    public        $name          = '';
    public        $subForm       = '';
    public        $options       = [];
    public        $inlineJS      = '';
    public        $size          = '';
    private       $_subformClass = "";

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'SubForm Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
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

    public function getSubFormClass() {
        if ($this->_subformClass == "") {
            $this->_subformClass = Helper::explodeLast(".", $this->subForm);
        }
        return $this->_subformClass;
    }

    public function getCtrlName() {
        return $this->subFormClass . ucfirst($this->name);
    }

    public function includeJS() {
        return ['modal-dialog.js'];
    }

    public function renderSubForm() {
        if ($this->subFormClass == get_class($this)) {
            return '<center><i class="fa fa-warning"></i> Error Rendering SubForm: Subform can not be the same as its parent</center>';
        } else {
            ## render
            Yii::import($this->subForm);
            $fb     = FormBuilder::load($this->subFormClass);
            $render = $fb->render($fb->model, [
                'wrapForm' => false
            ]);

            return $render;
        }
    }

    private function renderController() {
        $jspath = explode(".", $this->subForm);
        array_pop($jspath);
        $jspath   = implode(".", $jspath);
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

}