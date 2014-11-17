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
    public $columns = [];
    public $gridOptions = [];

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
        return array (
            array (
                'label' => 'Data Filter Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
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
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Generate Columns',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \\\'\\\'',
                    'ng-click' => 'generateColumns()',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Grid Options',
                'name' => 'gridOptions',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array (
                'value' => '<div style=\\"margin-top:5px\\"></div>',
                'type' => 'Text',
            ),
            array (
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
                ),
                'type' => 'ListView',
            ),
        );
    }

    private static function generateOrderParams($params, $template, $paramOptions = []) {
        $sqlparams = [];
        $sql = [];

        if (!is_array($params)) {
            $params = ['order_by' => []];
        }

        $tmpl = preg_replace("/order\s+by/i", "", $template);
        $rawOrder = explode(",", trim(str_replace("[order]", "", $tmpl)));
        foreach ($rawOrder as $o) {
            $o = trim(preg_replace('!\s+!', ' ', $o));
            $o = explode(" ", $o);
            if (count($o) == 2) {
                $index = -1;
                foreach ($params['order_by'] as $k => $p) {
                    if (@$p['fields'] == $o[0]) {
                        $index = $k;
                    }
                }

                if ($index < 0) {
                    array_unshift($params['order_by'], [
                        'field' => $o[0],
                        'direction' => $o[1]
                    ]);
                } else {
                    $params['order_by'][$index] = [
                        'field' => $o[0],
                        'direction' => $o[1]
                    ];
                }
            }
        }

        if (isset($params['order_by']) && count($params['order_by']) > 0) {
            foreach ($params['order_by'] as $k => $o) {
                $direction = $o['direction'] == 'asc' ? 'asc' : 'desc';
                $field = preg_replace("[^a-zA-Z0-9]", "", $o['field']);

                $sql[] = "{$field} {$direction}";
            }
        }

        $query = '';
        if (count($sql) > 0) {
            if ($template == '' || trim($template) == '[order]') {
                $query = "order by " . implode(" , ", $sql);
            } else {
                if (stripos("order by", $template) !== false) {
                    $query = str_replace("[order]", implode(" , ", $sql), $template);
                } else {
                    $query = "order by " . array_pop($sql);
                }
            }
        } else if (count(@$paramOptions) > 0) {
            $query = "order by " . @$paramOptions[0];
        }

        return [
            'sql' => $query,
            'params' => [],
            'render' => true,
            'generateTemplate' => true
        ];
    }

    public static function generatePagingParams($params, $paramOptions = []) {
        if (!isset($params['currentPage']) || count($params['currentPage']) == 0) {
            $defaultPageSize = "25";

            $template = [
                'sql' => "limit {$defaultPageSize}",
                'params' => [],
                'render' => true
            ];
        } else {
            $currentPage = $params['currentPage'];
            $pageSize = $params['pageSize'];
//            $totalItems = $params['totalServerItems'];

            $from = ($currentPage - 1) * $pageSize;
            $from = $from < 0 ? 0 : $from;

            $to = $currentPage * $pageSize;
            
            $template = [
                'sql' => "limit {$from},{$to}",
                'params' => [
    //                    'limit' => $pageSize,
    //                    'offset' => $from,
    //                    'page' => $currentPage,
    //                    'pageSize' => $pageSize
                ],
                'render' => true
            ];
        }
        return $template;
    }

    public static function generateParams($paramName, $params, $template, $paramOptions = []) {
        switch ($paramName) {
            case "order":
                return DataGrid::generateOrderParams($params, $template, $paramOptions);
            case "paging":
                return DataGrid::generatePagingParams($params, $paramOptions);
        }
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut datafilter dari hasil render
     */
    public function render() {

        foreach ($this->columns as $k => $c) {


            switch ($c['columnType']) {
                case 'dropdown':
                    $list = '[]';
                    if (@$c['listType'] == 'php') {
                        $list = $this->evaluate(@$c['listExpr'], true);
                        $list = json_encode($list);
                    }
                    $this->columns[$k]['listItem'] = $list;
                    break;
                case 'string':
                    if (count(@$this->columns[$k]['stringAlias']) > 0) {
                        foreach ($this->columns[$k]['stringAlias'] as $x => $y) {
                            $this->columns[$k]['stringAlias'][$x] = htmlentities($y);
                        }
                    }
                    break;
            }
        }

        return $this->renderInternal('template_render.php');
    }

}