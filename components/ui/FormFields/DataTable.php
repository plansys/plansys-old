<?php

/**
 * Class DataTable
 * @author rizky
 */
class DataTable extends FormField {

    /** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;

    /** @var string $filters */
    public $columns = [];
    public $gridOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Data Table";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-file-excel-o fa-nm";

    /**
     * @return array me-return array property DataTable.
     */
    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Data Filter Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Data Source Name',
                'name' => 'datasource',
                'options' => array(
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Generate Columns',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array(
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \\\'\\\'',
                    'ng-click' => 'generateColumns()',
                ),
                'type' => 'LinkButton',
            ),
            array(
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array(
                'label' => 'DataTable Options',
                'name' => 'gridOptions',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array(
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array(
                'value' => '<div style=\\"margin-top:5px\\"></div>',
                'type' => 'Text',
            ),
            array(
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataTableListForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array(
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
                ),
                'type' => 'ListView',
            ),
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['js'];
    }

    public function includeCSS() {
        return ['css/jquery.handsontable.full.min.css', 'css/data-table.css'];
    }

}
