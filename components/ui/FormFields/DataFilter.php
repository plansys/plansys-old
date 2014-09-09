<?php

/**
 * Class DataFilter
 * @author rizky
 */
class DataFilter extends FormField {

    /** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;

    /** @var string $filters */
    public $filters = array();
    public $options = array();

    /** @var string $toolbarName */
    public static $toolbarName = "Data Filter";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-filter";
    public $filterOperators = array(
        'string' => array(
            'Contains',
            'Does Not Contain',
            'Is Equal To',
            'Starts With',
            'Ends With',
            'Is Any Of',
            'Is Not Any Of',
            'Is Empty'
        ),
        'number' => array(
            '=',
            '<>',
            '>',
            '>=',
            '<=',
            '<',
            'Is Empty'
        ),
        'date' => array(
            'Between',
            'Not Between',
            'Less Than',
            'More Than'
        ),
        'list' => array(
            ''
        ),
        'check' => array(
            ''
        )
    );

    /**
     * @return array me-return array property DataFilter.
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
                'label' => 'Generate Filters',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \'\'',
                    'ng-click' => 'generateFilters()',
                ),
                'type' => 'LinkButton',
            ),
            '<div class="clearfix"></div>',
            array (
                'title' => 'Filters',
                'type' => 'SectionHeader',
            ),
            array (
                'renderInEditor' => 'No',
                'value' => '<div style=\\"margin-top:-13px;\\"></div>',
                'type' => 'Text',
            ),
            array (
                'name' => 'filters',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataFilterListForm',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'active.filters',
                    'ng-change' => 'save()',
                    'ps-after-add' => 'value.show = true;',
                ),
                'type' => 'ListView',
            ),
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('data-filter.js');
    }

    protected static function buildSingleParam($paramName, $column, $filter) {
        $sql = "";
        $param = "";
        switch ($filter['type']) {
            case "string":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    switch ($filter['operator']) {
                        case "Contains":
                            $sql = "{$column} LIKE :{$paramName}_{$column}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Does Not Contain":
                            $sql = "{$column} NOT LIKE :{$paramName}_{$column}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Is Equal To":
                            $sql = "{$column} LIKE :{$paramName}_{$column}";
                            $param = "{$filter['value']}";
                            break;
                        case "Starts With":
                            $sql = "{$column} LIKE :{$paramName}_{$column}";
                            $param = "{$filter['value']}%";
                            break;
                        case "Ends With":
                            $sql = "{$column} LIKE :{$paramName}_{$column}";
                            $param = "%{$filter['value']}";
                            break;
                        case "Is Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = array();
                            $psql = array();
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$column}_{$k}"] = "%{$p}%";
                                $psql[] = "{$column} LIKE :{$paramName}_{$column}_{$k}";
                            }
                            $sql = "(" . implode(" OR ", $psql) . ")";
                            break;
                        case "Is Not Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = array();
                            $psql = array();
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$column}_{$k}"] = "%{$p}%";
                                $psql[] = "{$column} NOT LIKE :{$paramName}_{$column}_{$k}";
                            }
                            $sql = "(" . implode(" AND ", $psql) . ")";
                            break;
                        case "Is Empty":
                            $sql = "({$column} LIKE '' OR {$column} IS NULL)";
                            break;
                    }
                }
                break;
            case "number":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    switch ($filter['operator']) {
                        case "=":
                        case "<>":
                        case ">":
                        case '>':
                        case '>=':
                        case '<=':
                        case '<':
                            $sql = "{$column} {$filter['operator']} :{$paramName}_{$column}";
                            $param = "{$filter['value']}";
                            break;
                        case "Is Empty":
                            $sql = "({$column} IS NULL)";
                            break;
                    }
                }
                break;
            case "date":
                switch ($filter['operator']) {
                    case "Between":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $sql = "({$column} BETWEEN :{$paramName}_{$column}_from AND :{$paramName}_{$column}_to)";
                            $param = array(
                                ":{$paramName}_{$column}_from" => @$filter['value']['from'],
                                ":{$paramName}_{$column}_to" => @$filter['value']['to'],
                            );
                        }
                        break;
                    case "Not Between":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $sql = "({$column} NOT BETWEEN :{$paramName}_{$column}_from AND :{$paramName}_{$column}_to)";
                            $param = array(
                                ":{$paramName}_{$column}_from" => @$filter['value']['from'],
                                ":{$paramName}_{$column}_to" => @$filter['value']['to'],
                            );
                        }
                        break;
                    case "More Than":
                        if (@$filter['value']['from'] != '') {
                            $sql = "{$column} > :{$paramName}_{$column}";
                            $param = @$filter['value']['from'];
                        }
                        break;
                    case "Less Than":
                        if (@$filter['value']['to'] != '') {
                            $sql = "{$column} < :{$paramName}_{$column}";
                            $param = @$filter['value']['to'];
                        }
                        break;
                }
                break;
            case "list":
                if ($filter['value'] != '') {
                    $sql = "{$column} LIKE :{$paramName}_{$column}";
                    $param = @$filter['value'];
                }
                break;
            case "check":
                if ($filter['value'] != '') {
                    $param = array();
                    $psql = array();
                    foreach ($filter['value'] as $k => $p) {
                        $param[":{$paramName}_{$column}_{$k}"] = "{$p}";
                        $psql[] = ":{$paramName}_{$column}_{$k}";
                    }
                    $sql = "{$column} IN (" . implode(", ", $psql) . ")";
                }
                break;
        }
        return array('sql' => $sql, 'param' => $param);
    }

    public static function generateParams($paramName, $params) {

        $sql = array();
        $flatParams = array();

        if (is_array($params) && count($params) > 0) {

            foreach ($params as $column => $filter) {
                $param = DataFilter::buildSingleParam($paramName, $column, $filter);
                $sql[] = $param['sql'];
                if (is_array($param['param'])) {
                    foreach ($param['param'] as $key => $value) {
                        $flatParams[$key] = $value;
                    }
                } else {
                    $flatParams[$paramName . "_" . $column] = $param['param'];
                }
            }
        }

        $template = array(
            'sql' => implode(" AND ", $sql),
            'params' => $flatParams
        );


        return $template;
    }

    /**
     * @return array me-return array hasil proses expression.
     */
    public function processExpr() {
        if (count($this->filters) == 0)
            return array();

        foreach ($this->filters as $k => $filter) {
            if ($filter['filterType'] == "list" || $filter['filterType'] == "check") {
                $listExpr = @$filter['listExpr'];
                $list = array();

                if ($listExpr != "") {
                    ## evaluate expression
                    $list = $this->evaluate($listExpr, true);

                    ## change sequential array to associative array
                    if (is_array($list) && !Helper::is_assoc($list)) {
                        $list = Helper::toAssoc($list);
                    }
                } else if (is_array($list) && !Helper::is_assoc($list)) {
                    $list = Helper::toAssoc($this->list);
                }

                $this->filters[$k]['list'] = $list;
            }
        }

        return array(
            'filters' => $this->filters
        );
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
