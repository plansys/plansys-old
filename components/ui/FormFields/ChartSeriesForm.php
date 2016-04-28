<?php

class ChartSeriesForm extends Form {
	
	/** @var string $label */
	public $label = '';
	
	/** @var string $value */
    public $value;
	
	/** @var string $color */
    public $color;
	
	/** @var bool $isTick */
    public $isTick;
	
	/** @var string $columnOptions */
    public $columnOptions = [];  
	
    public function getFields() {
        return  [
             [
                'value' => '<div ng-init=\"value[$index].show = false\" style=\"cursor:pointer;padding-bottom:1px;\" ng-click=\"value[$index].show = !value[$index].show\">
<div class=\"label data-filter-name pull-right\"> 
{{value[$index].columnType}}</div>
{{value[$index].label}} 
<div class=\"clearfix\"></div>
</div>',
                'type' => 'Text',
            ],
             [
                'value' => '<hr ng-show=\"value[$index].show\"
style=\"margin:4px -12px 6px -4px;float:left;width:100%;padding:0px 4px;\" />',
                'type' => 'Text',
            ],
             [
                'value' => '<div ng-show=\'value[$index].show\'>',
                'type' => 'Text',
            ],
             [
                'label' => 'Label',
                'name' => 'label',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' =>  [
                    'ng-model' => 'value[$index].label',
                    'ng-change' => 'updateListView()',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Color',
                'name' => 'color',
                'labelWidth' => '2',
                'fieldWidth' => '10',
                'options' =>  [
                    'ng-model' => 'value[$index].color',
                    'ng-change' => 'updateListView()',
                ],
                'type' => 'ColorPicker',
            ],
             [
                'value' => '<div style=\'margin-top:10px;\'></div>',
                'type' => 'Text',
            ],
             [
                'value' => '</div>',
                'type' => 'Text',
            ],
        ];
    }

    public function getForm() {
        return [
            'formTitle' => 'DataFilterListForm',
            'layout' => [
                'name' => 'full-width',
                'data' => [
                    'col1' => [
                        'type' => 'mainform',
                    ],
                ],
            ],
        ];
    }

      
}