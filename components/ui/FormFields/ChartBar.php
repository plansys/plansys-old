<?php

/**
 * Class DataSource
 * @author rizky
 */
class ChartBar extends FormField {
	
	/** @var string $colWidth */
    public $colWidth = 12;
	
	/** @var string $toolbarName */
    public static $toolbarName = "Bar Chart";

    /** @var string $category */
    public static $category = "Charts";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-bar-chart";
	
	/** @var string $chartType */
    public $chartType = 'Bar';
	
	/** @var string $retrieveMode */
    public $retrieveMode = 'by Column';

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
	
	/** @var string $tickSeries */
    public $tickSeries;
	
	/** @var array $options */
    public $options = [];
	
	
    /**
     * @return array Fungsi ini akan me-return array property DataSource.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Chart Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Chart Title',
                'name' => 'chartTitle',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.chartTitle',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Chart Type',
                'name' => 'chartType',
                'options' => array (
                    'ng-model' => 'active.chartType',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'Bar\', \'Column\')',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Data Source Name',
                'name' => 'datasource',
                'options' => array (
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                ),
                'list' => array (),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'name' => 'retrieveMode',
                'options' => array (
                    'ng-model' => 'active.retrieveMode',
                    'ng-change' => 'save()',
                ),
                'type' => 'HiddenField',
            ),
            array (
                'totalColumns' => '1',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Generate Series',
                        'buttonType' => 'success',
                        'icon' => 'magic',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;margin:10px 0px 5px 0px',
                            'ng-show' => 'active.datasource != \'\'',
                            'ng-click' => 'generateSeries(active.retrieveMode)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'w1' => '100%',
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'allowExtractKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Series',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"margin-top:5px;\"></div>
',
            ),
            array (
                'label' => 'Set as Tick',
                'name' => 'tickSeries',
                'options' => array (
                    'ng-model' => 'active.tickSeries',
                    'ng-change' => 'save()',
                    'ps-list' => 'tickSeriesList',
                ),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"margin-bottom:5px;\"></div>
<hr />',
            ),
            array (
                'name' => 'series',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.ChartSeriesForm',
                'options' => array (
                    'ng-model' => 'active.series',
                    'ng-change' => 'save(); setTickSeries();',
                    'ps-after-add' => 'value.show = true',
                ),
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                ),
                'type' => 'ListView',
            ),
        );
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
        return ['chart-bar.js'];
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