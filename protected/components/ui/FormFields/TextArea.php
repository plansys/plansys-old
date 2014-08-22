<?php
/**
 * Class TextArea
 * @author rizky
 */
class TextArea extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property TextArea.
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
                'totalColumns' => '3',
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '11',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Field Height',
                        'name' => 'fieldHeight',
                        'layout' => 'Vertical',
                        'labelWidth' => '11',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldHeight',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column3' => array (
                    '<column-placeholder></column-placeholder>',
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '11',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
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
	
	/** @var string $value */
    public $value = '';
	
	/** @var integer $labelWidth */
    public $labelWidth = 4;
	
	/** @var integer $fieldWidth */
    public $fieldWidth = 8;
	
	/** @var string $layout */
    public $layout = 'Horizontal';
	
	/** @var integer $fieldHeight */
    public $fieldHeight = 3;
	
	/** @var array $options */
    public $options = array();
	
	/** @var array $labelOptions */
    public $labelOptions = array();
	
	/** @var array $fieldOptions */
    public $fieldOptions = array();
	
	/** @var string $toolbarName */
    public static $toolbarName = "Text Area";
	
	/** @var string $category */
    public static $category = "User Interface";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-sort-alpha-asc";
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS()
    {
        return array('text-area.js');
    }

	/**
	 * @return string Fungsi ini akan me-return string class layout yang digunakan. Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout.
	*/
    public function getLayoutClass()
    {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

	/**
	 * @return string Fungsi ini akan me-return string class error jika terdapat error pada satu atau banyak attribute.
	*/
    public function getErrorClass()
    {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

	/**
	 * @return string Fungsi ini akan me-return string class label. Fungsi akan mengecek $layout untuk menentukan layout yang digunakan. Fungsi juga me-load option label dari property $labelOptions. 
	 */
    public function getlabelClass()
    {
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
    public function getFieldColClass()
    {
        return "col-sm-" . $this->fieldWidth;
    }

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */	
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->name;
        $this->fieldOptions['name'] = $this->name;
        $this->fieldOptions['rows'] = $this->fieldHeight;
        $this->addClass('form-control', 'fieldOptions');
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        return $this->renderInternal('template_render.php');
    }

}
