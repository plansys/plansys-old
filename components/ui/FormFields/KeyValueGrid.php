<?php
/**
 * Class KeyValueGrid
 * @author rizky
 */
class KeyValueGrid extends FormField {
    /**
     * @return array me-return array property KeyValueGrid.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'fieldname',
                'options' => array (
                    'ng-model' => 'active.fieldname',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'list' => array (),
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            '<hr/>',
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Grid Initial State',
                'name' => 'show',
                'options' => array (
                    'ng-model' => 'active.show',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'Show' => 'Show',
                    'Hide' => 'Hide',
                ),
                'listExpr' => 'array(
   \'Show\',
   \'Hide\',
)',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Empty Key',
                'name' => 'allowEmptyKey',
                'options' => array (
                    'ng-model' => 'active.allowEmptyKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Space in Key',
                'name' => 'allowSpaceOnKey',
                'options' => array (
                    'ng-model' => 'active.allowSpaceOnKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Double Quote',
                'name' => 'allowDoubleQuote',
                'options' => array (
                    'ng-model' => 'active.allowDoubleQuote',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ),
                'list' => array (
                    'No' => 'No',
                    'Yes' => 'Yes',
                ),
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'show' => 'Show',
                'options' => array (
                    'ng-model' => 'active.options',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ),
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /** @var string $label */
    public $label = '';
	
    /** @var string $fieldname */
    public $fieldname = '';
	
    /** @var string $value */
    public $value = '';
	
    /** @var string $show */
    public $show = 'Hide';
	
    /** @var array $options */
    public $options = array();
	
    /** @var string $allowEmptyKey */
    public $allowEmptyKey = 'No';
	
    /** @var string $allowSpaceOnKey */
    public $allowSpaceOnKey = 'No';
	
    /** @var string $allowDoubleQuote */
    public $allowDoubleQuote = 'No';
	
    /** @var string $toolbarName */
    public static $toolbarName = "KeyValue Grid";
	
    /** @var string $category */
    public static $category = "Data & Tables";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-key";
	
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('key-value-grid.js');
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut KeyValueGrid dari hasil render
     */
    public function render() {
        $ngModelAvailable = isset($this->options['ng-model']) 
            && is_string($this->options['ng-model']) 
            && trim($this->options['ng-model']) != "";
        
        if ($this->fieldname != '' && !$ngModelAvailable) {
            $this->options['ng-model'] = 'active.' . $this->fieldname;
        }
        return $this->renderInternal('template_render.php');
    }

}