<?php

class RadioButtonList extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-form-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array (),
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
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Horizontal' => 'Horizontal',
                    'Vertical' => 'Vertical',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Item Layout',
                'name' => 'itemLayout',
                'options' => array (
                    'ng-model' => 'active.itemLayout',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Horizontal' => 'Horizontal',
                    'Vertical' => 'Vertical',
                    'ButtonGroup' => 'ButtonGroup',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\',\'ButtonGroup\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label Width',
                'name' => 'labelWidth',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-model' => 'active.labelWidth',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-disabled' => 'active.layout == \'Vertical\';',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Radio Button Item',
                'fieldname' => 'list',
                'options' => array (
                    'ng-hide' => 'active.listExpr != \'\' || active.options[\'ng-form-list\'] != null',
                ),
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'validAction' => 'active.list = result;save();',
                'options' => array (
                    'ng-hide' => 'active.options[\'ng-form-list\'] != null',
                ),
                'desc' => '<i class="fa fa-warning"></i> WARNING: Using List Expression will replace <i>Radio Button Item</i> with expression result',
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'fieldname' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'fieldname' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public $label = '';
    public $name = '';
    public $value = '';
    public $list = '';
    public $listExpr = '';
    public $layout = 'Horizontal';
    public $itemLayout = 'Vertical';
    public $labelWidth = 4;
    public $options = array();
    public $labelOptions = array();
    public $fieldOptions = array();
    public static $toolbarName = "RadioButton List";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-dot-circle-o";

    public function includeJS() {
        return array('radio-button-list.js');
    }

    public function processExpr() {
        if ($this->listExpr != "") {
            ## evaluate expression
            $this->list = $this->evaluate($this->listExpr, true);
            
            ## change sequential array to associative array
            if (is_array($this->list) && !Helper::is_assoc($this->list)) {
                $this->list = Helper::toAssoc($this->list);
            }

            if (FormField::$inEditor) {
                if (count($this->list) > 5) {
                    $this->list = array_slice($this->list, 0, 5);
                    $this->list['z...'] = "...";
                }
            }
        } else if (is_array($this->list) && !Helper::is_assoc($this->list)) {
            $this->list = Helper::toAssoc($this->list);
        }

        return array(
            'list' => $this->list
        );
    }

    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    public function checked($value) {
        return $value == $this->value ? 'checked="checked"' : '';
    }

    public function getFieldColClass() {
        return "col-sm-" . ($this->layout == 'Vertical' ? 12 : 12 - $this->labelWidth);
    }

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
