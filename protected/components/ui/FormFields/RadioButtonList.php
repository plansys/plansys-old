<?php

class RadioButtonList extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property RadioButton.
	 */
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

	/** @var string variable untuk menampung label */
    public $label = '';
	
	/** @var string variable untuk menampung name */
    public $name = '';
	
	/** @var string variable untuk menampung value yang digunakan pada function checked */
    public $value = '';
	
	/** @var string variable untuk menampung list */
    public $list = '';
	
	/** @var string variable untuk menampung list expression yang digunakan pada function processExpr */
    public $listExpr = '';
	
	/** @var string variable ntuk menampung layout */
    public $layout = 'Horizontal';
	
	/** @var string variable untuk menampung property layout */
    public $itemLayout = 'Vertical';
	
	/** @var integer variable untuk menampung nilai width label */
    public $labelWidth = 4;
	
	/** @var array variable untuk menampung array options */
    public $options = array();
	
	/** @var array variable untuk menampung array options label */
    public $labelOptions = array();
	
	/** @var array variable untuk menampung array options field */
    public $fieldOptions = array();
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "RadioButton List";
	
	/** @var string variable untuk menampung category */
    public static $category = "User Interface";
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-dot-circle-o";
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS() {
        return array('radio-button-list.js');
    }

	/**
	 * @return array Fungsi ini akan memproses expression menjadi array lalu mereturn array tersebut.
	*/
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

	/**
	 * @return string Fungsi ini akan me-return string class layout yang digunakan. Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout.
	*/
    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

	/**
	 * @return string Fungsi ini akan me-return string class error jika terdapat error pada satu atau banyak attribute.
	*/
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

	/**
	 * @return string Fungsi ini akan me-return string class label. Fungsi akan mengecek $layout untuk menentukan layout yang digunakan. Fungsi juga me-load option label dari property $labelOptions. 
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
	 * @param string $value Parameter untuk melempar value field.
	 * @return boolean Fungsi ini untuk mengecek value dari field.
	 */
    public function checked($value) {
        return $value == $this->value ? 'checked="checked"' : '';
    }

	/**
	 * @return integer Fungsi ini akan me-return string class untuk menentukan width fields.
	 */	
    public function getFieldColClass() {
        return "col-sm-" . ($this->layout == 'Vertical' ? 12 : 12 - $this->labelWidth);
    }

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */
    public function render() {
        $this->addClass('form-group form-group-sm');
        $this->addClass($this->layoutClass);
        $this->addClass($this->errorClass);

        $this->addClass('input-group', 'fieldOptions');
        if ($this->itemLayout == "Horizontal") {
            $this->addClass('inline', 'fieldOptions');
        }

        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
