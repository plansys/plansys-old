<?php

/**
 * Class DataGrid
 * @author rizky
 */
class DataGrid extends FormField {

    /** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;

    /** @var string $filters */
    public $columns = array();
    public $gridOptions = array();

    /** @var string $toolbarName */
    public static $toolbarName = "Data Grid";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-table fa-nm";

    /**
     * @return array me-return array property DataGrid.
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
                    'ng-show' => 'active.datasource != \'\'',
                    'ng-click' => 'generateColumns()',
                ),
                'type' => 'LinkButton',
            ),
            array(
                'renderInEditor' => 'No',
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array(
                'label' => 'Grid Options',
                'fieldname' => 'gridOptions',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array(
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array(
                'renderInEditor' => 'No',
                'value' => '<div style=\\"margin-top:-13px;\\"></div>',
                'type' => 'Text',
            ),
            array(
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array(
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
                    'ps-after-add' => 'value.show = true;',
                ),
                'type' => 'ListView',
            ),
        );
    }

    private static function generateOrderParams($params) {
        if (!isset($params['order_by']) || count($params['order_by']) == 0) {
            $template = array(
                'sql' => 'order by id asc',
                'params' => array(),
                'render' => true
            );
        } else {
            $sqlparams = array();
            $sql = array();
            foreach ($params['order_by'] as $k => $o) {
                $direction = $o['direction'] == 'asc' ? 'asc' : 'desc';
                $field = preg_replace("[^a-zA-Z0-9]", "", $o['field']);

                $sql[] = "{$field} {$direction}";
            }

            $template = array(
                'sql' => count($sql) > 0 ? 'order by ' . implode(", ", $sql) : '',
                'params' => array(),
                'render' => true,
            );
        }
        return $template;
    }

    public static function generatePagingParams($params) {
        return array(
            'sql' => '',
            'params' => array()
        );
    }

    public static function generateParams($paramName, $params) {
        switch ($paramName) {
            case "order":
                return DataGrid::generateOrderParams($params);
            case "paging":
                return DataGrid::generatePagingParams($params);
        }
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('ng-grid-plugins.js', 'data-grid.js');
    }

    public function processExpr() {

        return array();
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut datafilter dari hasil render
     */
    public function render() {
        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}
