<?php

/**
 * Class DataSource
 * @author rizky
 */
class ChartPie extends FormField {

	/** @var string $chartWidth */
    public $chartWidth = 200;
	
	/** @var string $chartHeight */
    public $chartHeight = 200;
	
	/** @var string $colWidth */
    public $colWidth = 12;
	
	/** @var string $toolbarName */
    public static $toolbarName = "Pie Chart";

    /** @var string $category */
    public static $category = "Charts";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-pie-chart";
	
	

	/** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;
	
	/** @var string $chartTitle */
    public $chartTitle;
	
	/** @var string $colorArray */
	public $colorArray = array();
	
	/** @var string $series */
    public $series;
	
	/** @var string $options */
    public $options = array();
	
	
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
                'renderInEditor' => 'Yes',
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Generate Columns',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \\\'\\\'',
                    'ng-click' => 'generateSeries()',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Chart Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'series',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.ChartPieColumnsForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'active.series',
                    'ng-change' => 'save()',
                    'ps-after-add' => 'value.show = true',
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
        return array('chart-pie.js');
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