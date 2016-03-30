<?php

class PopupWindow extends FormField {

    public static $toolbarName = "Popup Window";
    public static $category    = "Layout";
    public static $toolbarIcon = "fa fa-object-ungroup";
    public        $type        = 'PopupWindow'; //subform, url
    public        $name        = '';
    public        $options     = [];
    public        $mode        = 'subform';
    public        $subForm     = '';
    public        $url         = '';
    public        $title       = '';
    public        $parentForm  = '';

    public function includeJS() {
        return ['popup-window.js'];
    }

    public function actionSubform($c, $f) {
        Yii::import($c);
        $class = Helper::explodeLast(".", $c);
        $fb    = FormBuilder::load($class);
        $field = $fb->findField(['name' => $f]);
        if (!!$field) {
            $this->attributes = $field;
            $this->renderSubForm();
        }
    }

    public function renderSubForm() {
        $class = Helper::explodeLast(".", $this->subForm);
        if ($class == get_class($this)) {
            return '<center><i class="fa fa-warning"></i> Error Rendering SubForm: Subform can not be the same as its parent</center>';
        } else {
            ## render
            Yii::import($this->subForm);
            $ctrl = Yii::app()->controller;
            
            $ctrl->renderForm($class, null, [], [
                'layout' => '//layouts/blank'
            ]);
        }
    }

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Popup Title',
                'name' => 'title',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-delay' => '500',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.mode == \'url\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Mode',
                'name' => 'mode',
                'options' => array (
                    'ng-model' => 'active.mode',
                    'ng-change' => 'save()',
                ),
                'list' => array (
                    'subform' => 'SubForm',
                    'url' => 'Url',
                ),
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SubForm',
                'name' => 'subForm',
                'options' => array (
                    'ng-model' => 'active.subForm',
                    'ng-change' => 'save()',
                    'ng-if' => 'active.mode == \'subform\'',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => 'FormBuilder::listForm()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Edit Subform',
                'icon' => 'sign-in',
                'position' => 'right',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px;',
                    'href' => 'url:/dev/forms/update?class={active.subForm}',
                    'target' => '_blank',
                    'ng-if' => 'active.mode == \'subform\'',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-init=\\"active.parentForm = classPath\\"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\\"clearfix\\"></div>',
            ),
            array (
                'label' => 'Url',
                'fieldname' => 'url',
                'language' => 'js',
                'options' => array (
                    'ng-model' => 'active.url',
                    'ng-delay' => '500',
                    'ng-if' => 'active.mode == \'url\'',
                    'ng-change' => 'save()',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}