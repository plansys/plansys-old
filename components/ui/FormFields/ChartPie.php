<?php

/**
 * Class DataSource
 * @author rizky
 */
class ChartPie extends FormField {
	
	/** @var string $colWidth */
    public $colWidth = 12;
	
	/** @var string $toolbarName */
    public static $toolbarName = "Pie Chart";

    /** @var string $category */
    public static $category = "Charts";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-pie-chart";
	
	/** @var string $chartType */
    public $chartType = 'Pie';
	
	/** @var string $retrieveMode */
    public $retrieveMode = 'by Row';
	
	

	/** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;
	
	/** @var string $chartTitle */
    public $chartTitle;
	
	/** @var array $colorArray */
	public $colorArray = [];
	
	/** @var string $series */
    public $series;
	
	/** @var array $options */
    public $options = [];
	
	
    /**
     * @return array Fungsi ini akan me-return array property DataSource.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Chart Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' =>  [
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Chart Title',
                'name' => 'chartTitle',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' =>  [
                    'ng-model' => 'active.chartTitle',
                    'ng-change' => 'save()',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Data Source Name',
                'name' => 'datasource',
                'options' =>  [
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                ],
                'list' =>  [],
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ],
             [
                'name' => 'retrieveMode',
                'options' =>  [
                    'ng-model' => 'active.retrieveMode',
                    'ng-change' => 'save()',
                ],
                'type' => 'HiddenField',
            ],
             [
                'value' => '<div style=\\"margin-top:25px\\"></div>',
                'type' => 'Text',
            ],
             [
                'totalColumns' => '1',
                'column1' =>  [
                     [
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ],
                     [
                        'label' => 'Generate Series',
                        'buttonType' => 'success',
                        'icon' => 'magic',
                        'buttonSize' => 'btn-xs',
                        'options' =>  [
                            'style' => 'float:right;margin:10px 0px 5px 0px',
                            'ng-show' => 'active.datasource != \\\'\\\'',
                            'ng-click' => 'generateSeries(active.retrieveMode)',
                        ],
                        'type' => 'LinkButton',
                    ],
                ],
                'type' => 'ColumnField',
            ],
             [
                'value' => '<div style=\\"margin-bottom:25px\\"></div>',
                'type' => 'Text',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'allowExtractKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
             [
                'title' => 'Series',
                'type' => 'SectionHeader',
            ],
             [
                'value' => '<div style=\\"margin-bottom:5px;\\"></div>',
                'type' => 'Text',
            ],
             [
                'name' => 'series',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.ChartSeriesForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' =>  [
                    'ng-model' => 'active.series',
                    'ng-change' => 'save()',
                    'ps-after-add' => 'value.show = true',
                ],
                'type' => 'ListView',
            ],
        ];
    }

	/**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
	public function getColClass() {
        return "col-sm-" . $this->colWidth;
    }
	
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['chart-pie.js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        return $this->renderInternal('template_render.php');
    }

}	