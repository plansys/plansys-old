<?php

/**
 * Class CheckboxList
 * @author rizky
 */
class CheckboxList extends FormField {

    /**
     * @return array me-return array property Checkbox.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Checkbox Mode',
                'name' => 'mode',
                'options' => array (
                    'ng-model' => 'active.mode',
                    'ng-change' => 'active.name = \'\';',
                ),
                'listExpr' => '[\'Default\',\'Relation\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    'ng-if' => 'active.mode == \'Default\'',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Relation Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'generateRelCheckbox();changeActiveName()',
                    'ps-list' => 'relFieldList',
                    'ng-if' => 'active.mode == \'Relation\'',
                    'ng-init' => 'generateRelCheckbox();',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Target Field',
                'name' => 'relField',
                'options' => array (
                    'ng-model' => 'active.relField',
                    'ng-change' => 'save()',
                    'ps-list' => 'relationFieldList',
                    'ng-show' => 'active.mode == \'Relation\'',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
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
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
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
                'label' => 'Convert List to String',
                'name' => 'convertToString',
                'options' => array (
                    'ng-model' => 'active.convertToString',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-if' => 'active.mode == \'Default\'',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'CheckBox Item',
                'name' => 'list',
                'show' => 'Show',
                'options' => array (
                    'ng-hide' => 'active.listExpr != \'\' || active.options[\'ps-list\'] != null',
                ),
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => array (
                    'ng-hide' => 'active.options[\'ps-list\'] != null',
                    'ps-valid' => 'active.list = result;save();',
                ),
                'desc' => '<i class=\'fa fa-warning\'></i> WARNING: Using List Expression will replace <i>CheckBox Item</i>with expression result',
                'type' => 'ExpressionField',
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

    /** @var string $value digunakan pada function checked */
    public $value = '';

    /** @var string $list */
    public $list = '';
    public $formattedList = '';

    /** @var string $listExpr */
    public $listExpr = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var string $itemLayout */
    public $itemLayout = 'Vertical';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var string $convertToString */
    public $convertToString = 'Yes';

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var array $options */
    public $options = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    public $mode = 'Default';
    
    public $relField = '';

    /** @var string $toolbarName */
    public static $toolbarName = "Checkbox List";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-check-square";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['check-box-list.js'];
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

        $this->formattedList = [];
        if (is_array($this->list)) {
            foreach ($this->list as $k=>$v) {
                $this->formattedList[] = [
                    'text' => $v,
                    'value' => $k
                ];
            }
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
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . ($this->layout == 'Vertical' ? 12 : 12 - $this->labelWidth);
    }

    /**
     * checked
     * Fungsi ini untuk mengecek value dari field ada dalam sebuah array list
     * @param string $value
     * @return string me-return string hasil checked
     */
    public function checked($value) {
        if ($this->convertToString == 'Yes') {
            $list = explode(',', $this->value);
        } else if (is_array($this->value)) {
            $list = $this->value;
        } else if (is_string($this->value)) {
            $list = [$this->value];
        } else {
            $list = [];
        }

        return in_array($value, $list) ? 'checked="checked"' : '';
    }

    public function getRelationInfo() {
        if ($this->mode == "Relation") {
            $rel = $this->model->metaData->relations[$this->name];
            $class = $rel->className;
            $model = $class::model();
            
            return [
                'foreignKey' => $rel->foreignKey,
                'type' => get_class($rel),
                'className' => $class,
                'parentPrimaryKey' => $this->model->tableSchema->primaryKey,
                'targetKey' => $this->relField,
                'attributes' => $model->getAttributesWithoutRelation()
            ];
        }
    }

    public function getPostName($mode = '') {
        return str_replace("]", $mode . "]", $this->renderName);
    }
    
    public function getDeleteData() {
        $relChanges   = $this->model->getRelChanges($this->name);
    
        return $relChanges['delete'];
    }
    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm');
        $this->addClass($this->layoutClass);
        $this->addClass($this->errorClass);

        $this->addClass('input-group', 'fieldOptions');
        $this->addClass('input-list', 'fieldOptions');
        if ($this->itemLayout == "Horizontal") {
            $this->addClass('inline', 'fieldOptions');
        }

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}