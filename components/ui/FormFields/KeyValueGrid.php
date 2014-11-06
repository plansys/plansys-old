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
        return  [
             [
                'label' => 'Field Name',
                'name' => 'name',
                'options' =>  [
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ],
                'list' =>  [],
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'value' => '<hr/>',
                'type' => 'Text',
            ],
             [
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' =>  [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Grid Initial State',
                'name' => 'show',
                'options' =>  [
                    'ng-model' => 'active.show',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(
   \'Show\',
   \'Hide\',
)',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Allow Empty Key',
                'name' => 'allowEmptyKey',
                'options' =>  [
                    'ng-model' => 'active.allowEmptyKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Allow Space in Key',
                'name' => 'allowSpaceOnKey',
                'options' =>  [
                    'ng-model' => 'active.allowSpaceOnKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\');',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Allow Double Quote',
                'name' => 'allowDoubleQuote',
                'options' =>  [
                    'ng-model' => 'active.allowDoubleQuote',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ],
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Allow Extract Key',
                'name' => 'allowExtractKey',
                'options' =>  [
                    'ng-model' => 'active.allowExtractKey',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'listExpr' => 'array(\\\'No\\\',\\\'Yes\\\')',
                'labelWidth' => '5',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'options' =>  [
                    'ng-model' => 'active.options',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ],
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    /** @var string $label */
    public $label = '';
	
    /** @var string $fieldname */
    public $name = '';
	
    /** @var string $value */
    public $value = [];
	
    /** @var string $show */
    public $show = 'Hide';
	
    /** @var array $options */
    public $options = [];
	
    /** @var string $allowEmptyKey */
    public $allowEmptyKey = 'No';
	
    /** @var string $allowSpaceOnKey */
    public $allowSpaceOnKey = 'No';
	
    /** @var string $allowDoubleQuote */
    public $allowDoubleQuote = 'No';
	
	/** @var string $allowExtractKey */
    public $allowExtractKey = 'No';
	
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
        return ['key-value-grid.js'];
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
        
        if ($this->name != '' && !$ngModelAvailable) {
            $this->options['ng-model'] = 'active.' . $this->name;
        }
        return $this->renderInternal('template_render.php');
    }

}