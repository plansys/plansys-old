<?php

/**
 * Class DataSource
 * @author rizky
 */
class ChartPie extends FormField {

	/** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;
	
	/** @var string $fieldWidth */
    public $colWidth = 8;
	
	/** @var string $colorArray */
	public $colorArray = array();
	
	/** @var string $chartWidth */
    public $chartWidth;
	
	/** @var string $chartHeight */
    public $chartHeight;
	
	/** @var string $chartHeight */
    public $position;
	
	/** @var string $options */
    public $options = array();
	
	/** @var boolean $isHidden */
    public $isHidden = false;

    /** @var string $toolbarName */
    public static $toolbarName = "Pie Chart";

    /** @var string $category */
    public static $category = "Charts";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-pie-chart";
	
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
                'label' => 'Column Width',
                'name' => 'colWidth',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.colWidth',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'totalColumns' => '4',
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Chart Width',
                        'name' => 'chartWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.chartWidth',
                            'ng-change' => 'save()',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column3' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Chart Height',
                        'name' => 'chartHeight',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.chartHeight',
                            'ng-change' => 'save()',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Color',
                'name' => 'colorArray',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
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
		$this->position = trim($this->position);
		$this->position = explode(',', $this->position);
		
        return $this->renderInternal('template_render.php');
    }

}	