<?php
/**
 * Class TextField
 * @author rizky
 */
class TextField extends FormField
{
    /**
     * @return array me-return array property TextField.
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
                'label' => 'Field Type',
                'name' => 'fieldType',
                'options' => array (
                    'ng-model' => 'active.fieldType',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'text' => 'Text Field',
                    'password' => 'Password Field',
                ),
                'showOther' => 'Yes',
                'otherLabel' => 'Other...',
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
                'column1' => array (
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column3' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'column4' => array (
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
            '<hr/>',
            array (
                'column1' => array (
                    array (
                        'name' => 'prefix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Prefix',
                        'options' => array (
                            'ng-model' => 'active.prefix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'name' => 'postfix',
                        'layout' => 'Vertical',
                        'fieldWidth' => '11',
                        'prefix' => 'Postfix',
                        'options' => array (
                            'ng-model' => 'active.postfix',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
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
	
    /** @var string $fieldType */
    public $fieldType = 'text';
	
    /** @var string $value */
    public $value = '';
	
    /** @var string $layout */
    public $layout = 'Horizontal';
	
    /** @var integer $labelWidth */
    public $labelWidth = 4;
	
    /** @var integer $fieldWidth */
    public $fieldWidth = 8;
	
    /** @var string $prefix */
    public $prefix = '';

    /** @var string $postfix */
    public $postfix = '';

    /** @var array $options */
    public $options = array();
	
    /** @var array $labelOptions */
    public $labelOptions = array();
	
    /** @var array $fieldOptions */
    public $fieldOptions = array();
	
    /** @var string $toolbarName */
    public static $toolbarName = "Text Field";
	
    /** @var string $category */
    public static $category = "User Interface";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-text-height";
	
    /**
     * @return array me-return array javascript yang di-include
    */
    public function includeJS()
    {
        return array('text-field.js');
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
     * dan me-load option label dari property $labelOptions
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
        return "col-sm-" . $this->fieldWidth;
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render()
    {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->renderID;
        $this->fieldOptions['name'] = $this->renderName;
        $this->addClass('form-control', 'fieldOptions');

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        return $this->renderInternal('template_render.php');
    }
}
