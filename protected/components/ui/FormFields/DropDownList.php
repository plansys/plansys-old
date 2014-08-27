<?php
/**
 * Class DropDownList
 * @author rizky
 */
class DropDownList extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property DropDown.
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
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
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
                'listExpr' => 'array(\\"Yes\\",\\"No\\")',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show \\"Other\\" Item',
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
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => '\\"Other\\" Item Label',
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
                    'ng-hide' => 'active.listExpr != \'\' || active.options[\'ps-list\'] != null',
                ),
                'allowEmptyKey' => 'Yes',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => array (
                    'ng-hide' => 'active.options[\'ps-list\'] != null',
                    'ps-list' => 'active.list = result;save();'
                ),
                'desc' => '<i class=\\"fa fa-warning\\"></i> WARNING: Using List Expression will replace <i>DropDown Item</i> with expression result',
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

	/** @var string $label */
    public $label = '';
	
	/** @var string $name */
    public $name = '';
	
	/** @var string $value digunakan pada function checked */
    public $value = '';
	
	/** @var array $options */
    public $options = array();
	
	/** @var array $fieldOptions */
    public $fieldOptions = array();
	
	/** @var array $labelOptions */
    public $labelOptions = array();
	
	/** @var string $list */
    public $list = '';
	
	/** @var string $listExpr digunakan pada function processExpr */
    public $listExpr = '';
	
	/** @var string $layout */
    public $layout = 'Horizontal';
	
	/** @var integer $labelWidth */
    public $labelWidth = 4;
	
	/** @var integer $fieldWidth */
    public $fieldWidth = 8;
	
	/** @var string $searchable */
    public $searchable = 'No';
	
	/** @var string $showOther */
    public $showOther = 'No';
	
	/** @var string $otherLabel */
    public $otherLabel = 'Lainnya';
	
	/** @var string $toolbarName */
    public static $toolbarName = "Drop Down List";
	
	/** @var string $category */
    public static $category = "User Interface";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-caret-square-o-down";
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS() {
        return array('drop-down-list.js');
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
	 * @param string $value Parameter untuk melempar value field.
	 * @return boolean Fungsi ini untuk mengecek value dari field.
	 */
    public function checked($value) {
        if ($this->value == $value)
            return true;
        else
            return false;
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
	 * @return integer Fungsi ini akan me-return string class untuk menentukan width fields.
	 */	
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

	/**
	 * @return string Fungsi ini akan me-return string class untuk button.
	 */	
    public function getFieldClass() {
        return "btn-group btn-block";
    }

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */	
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('btn dropdown-toggle btn-sm btn-block btn-dropdown-field', 'fieldOptions');
        $btn_class = ['btn-primary', 'btn-default', 'btn-success', 'btn-danger', 'btn-warning'];
        if (!in_array($this->fieldOptions['class'], $btn_class)) {
            $this->addClass('btn-default', 'fieldOptions');
        }

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        
        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
