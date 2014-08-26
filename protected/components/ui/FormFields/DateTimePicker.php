<?php
/**
 * Class DateTimePicker
 * @author rizky
 */
class DateTimePicker extends FormField
{
	/**
	 * @return array Fungsi ini akan me-return array property DateTimePicker.
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
                'label' => 'Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'datetime' => 'Date Time',
                    'date' => 'Date',
                    'time' => 'Time',
                ),
                'fieldWidth' => '4',
                'type' => 'DropDownList',
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
                'label' => 'Label Width',
                'name' => 'labelWidth',
                'fieldWidth' => '3',
                'options' => array (
                    'ng-model' => 'active.labelWidth',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-disabled' => 'active.layout == \'Vertical\'',
                ),
                'type' => 'TextField',
            ),
            '<hr/>',
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
            array (
                'label' => 'DatePicker Options',
                'fieldname' => 'datepickerOptions',
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
	
	/** @var string $layout */
    public $layout = 'Horizontal';
	
	/** @var integer $labelWidth */
    public $labelWidth = 4;
	
	/** @var integer $fieldWidth */
    public $fieldWidth = 5;
	
	/** @var string $prefix */
    public $prefix = '';
	
	/** @var string $postfix */
    public $postfix = '';
	
	/** @var array $options */
    public $options = array();
	
	/** @var array $labelOptions */
    public $labelOptions = array();
    
    public $fieldType = "date";
	
	/** @var array $fieldOptions */
    public $fieldOptions = array();
    
	/** @var array $fieldOptions */
    public $datepickerOptions = array(
        'show-weeks' => 'false'
    );
	
	
	/** @var string $toolbarName */
    public static $toolbarName = "Date Time Picker";
	
	/** @var string $category */
    public static $category = "User Interface";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-calendar";
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS()
    {
        return array('date-time-picker.js');
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
        return "col-sm-" . (12 - $this->labelWidth);
    }

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */	
    public function render()
    {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('form-control', 'fieldOptions');

        $this->setOption('datepicker-popup', 'dd/MM/yyyy', 'fieldOptions');
        $this->setOption('datepicker-options', 'dateOptions', 'fieldOptions');
        $this->setOption('datepicker-mode', 'day', 'fieldOptions');
        $this->setOption('is-open', 'dateOpened', 'fieldOptions');
        
        $this->setOption('showButtonBar', false, 'datepickerOptions');
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        return $this->renderInternal('template_render.php');
    }
}
