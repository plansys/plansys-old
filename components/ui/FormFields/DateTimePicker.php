<?php
/**
 * Class DateTimePicker
 * @author rizky
 */
class DateTimePicker extends FormField
{
    /**
     * @return array me-return array property DateTimePicker.
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
                ),
                'list' => array (),
                'searchable' => 'Yes',
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
                    'monthyear' => 'Month Year',
                    'time' => 'Time',
                ),
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
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
            array (
                'label' => 'Default To Today',
                'name' => 'defaultToday',
                'options' => array (
                    'ng-model' => 'active.defaultToday',
                    'ng-change' => 'save();',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
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
            array (
                'label' => 'DatePicker Options',
                'name' => 'datepickerOptions',
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
    public $options = [];
	
    /** @var array $labelOptions */
    public $labelOptions = [];
    
    /** @var string $fieldType */
    public $fieldType = "date";
	
    /** @var array $fieldOptions */
    public $fieldOptions = [];
    
    /** @var array $fieldOptions */
    public $datepickerOptions = [
        'show-weeks' => 'false'
    ];
    
    public $defaultToday = 'Yes';
	
    /** @var string $toolbarName */
    public static $toolbarName = "Date Time Picker";
	
    /** @var string $category */
    public static $category = "User Interface";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-calendar";
	
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS()
    {
        return ['date-time-picker.js'];
    }

    /**
     * getLayoutClass
     * Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout
     * @return string me-return string Class layout yang digunakan
     */
    public function getLayoutClass()
    {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    /**
     * @return string me-return string Class error jika terdapat error pada satu atau banyak attribute.
     */
    public function getErrorClass()
    {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    /**
     * getlabelClass
     * Fungsi ini akan mengecek $layout untuk menentukan layout yang digunakan
     * dan meload option label dari property $labelOptions
     * @return string me-return string Class label
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
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */	
    public function getFieldColClass()
    {
        return "col-sm-" . (12 - $this->labelWidth);
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field DateTimePicker dari hasil render
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
        
        $this->addClass('text-center', 'fieldOptions');
        
        $this->setOption('showButtonBar', false, 'datepickerOptions');
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        
        if (!is_string($this->value))
            $this->value = date("Y-m-d H:i:s");
        
        return $this->renderInternal('template_render.php');
    }
}