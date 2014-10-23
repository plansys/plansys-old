<?php

class ChartPieColumnsForm extends Form {
	
	/** @var string $label */
	public $label = '';
	
	/** @var string $value */
    public $value;
	
	/** @var string $color */
    public $color;
	
	/** @var string $columnOptions */
    public $columnOptions = array();  
	
    public function getFields() {
        return array (
            array (
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\"> 
{{value[$index].columnType}}</div>
{{value[$index].label}} 
<div class=\"clearfix\"></div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<hr ng-show=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
                'type' => 'Text',
            ),
            array (
                'value' => '<div ng-show=\\"value[$index].show\\">',
                'type' => 'Text',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' => array (
                    'ng-model' => 'value[$index].label',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Value',
                'name' => 'value',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' => array (
                    'ng-model' => 'value[$index].value',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Color',
                'name' => 'color',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' => array (
                    'ng-model' => 'value[$index].color',
                    'ng-change' => 'updateListView()',
                ),
                'type' => 'TextField',
            ),
            array (
                'value' => '<div style=\\"margin-bottom:-3px;\\"></div>',
                'type' => 'Text',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

    public function getForm() {
        return array(
            'formTitle' => 'DataFilterListForm',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

      
}