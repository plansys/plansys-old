<?php

class DropDownList extends FormField {

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
                'list' => array (
                    'Horizontal' => 'Horizontal',
                    'Vertical' => 'Vertical',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'totalColumns' => '4',
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
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
                    '<column-placeholder></column-placeholder>',
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
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            '<hr/>',
            array (
                'label' => 'Searchable',
                'name' => 'searchable',
                'options' => array (
                    'ng-model' => 'active.searchable',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
                'listExpr' => 'array("Yes","No")',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show "Other" Item',
                'name' => 'showOther',
                'options' => array (
                    'ng-model' => 'active.showOther',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => '"Other" Item Label',
                'name' => 'otherLabel',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.otherLabel',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-show' => 'active.showOther == \'Yes\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'DropDown Item',
                'fieldname' => 'list',
                'show' => 'Show',
                'options' => array (
                    'ng-hide' => 'active.listExpr != \'\' || active.options[\'ng-form-list\'] != null',
                ),
                'allowEmptyKey' => 'Yes',
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
                'desc' => '<i class="fa fa-warning"></i> WARNING: Using List Expression will replace <i>DropDown Item</i> with expression result',
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
    public $options = array();
    public $fieldOptions = array();
    public $labelOptions = array();
    public $list = '';
    public $listExpr = '';
    public $layout = 'Horizontal';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $searchable = 'No';
    public $showOther = 'No';
    public $otherLabel = 'Lainnya';
    public static $toolbarName = "Drop Down List";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-caret-square-o-down";

    public function includeJS() {
        return array('drop-down-list.js');
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

    public function checked($value) {
        if ($this->value == $value)
            return true;
        else
            return false;
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

    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function getFieldClass() {
        return "btn-group btn-block";
    }

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('btn dropdown-toggle btn-sm btn-block btn-dropdown-field', 'fieldOptions');
        $btn_class = ['btn-primary', 'btn-default', 'btn-success', 'btn-danger', 'btn-warning'];
        if (!in_array($this->fieldOptions['class'], $btn_class)) {
            $this->addClass('btn-default', 'fieldOptions');
        }

        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
