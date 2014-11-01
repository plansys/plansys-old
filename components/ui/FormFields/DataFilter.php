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
            'Is Any Of',
            'Is Not Any Of',
            'Contains',
            'Does Not Contain',
            'Is Equal To',
            'Starts With',
            'Ends With',
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
            'More Than',
            'Daily',
            'Weekly',
            'Monthly',
            'Yearly',
        )
    );

    public static function getFilterOperators($date = "") {
        $a = new DataFilter;
        $filters = $a->filterOperators;
        if ($date != "") {
            $filters = $filters[$date];
            $result = [];
            foreach ($filters as $i => $k) {
                $result[$k] = $k;
            }
        }
        return array('' => 'No Operator') + $result;
    }

    /**
     * @return array me-return array property DataFilter.
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
                'label' => 'Generate Filters',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array(
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \\\'\\\'',
                    'ng-click' => 'generateFilters()',
                ),
                'type' => 'LinkButton',
            ),
            array(
                'value' => '<div class=\\"clearfix\\"></div>',
                'type' => 'Text',
            ),
            array(
                'title' => 'Filters',
                'type' => 'SectionHeader',
            ),
            array(
                'value' => '<div style=\\"margin-top:5px;\\"></div>',
                'type' => 'Text',
            ),
            array(
                'name' => 'filters',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataFilterListForm',
                'labelWidth' => '0',
                'inlineJS' => 'DataFilter/inlinejs/dfr-init.js',
                'fieldWidth' => '12',
                'options' => array(
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
        $pcolumn = preg_replace('/[^\da-z]/i', '_', $column);
        switch ($filter['type']) {
            case "string":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    switch ($filter['operator']) {
                        case "Contains":
                            $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Does Not Contain":
                            $sql = "{$column} NOT LIKE :{$paramName}_{$pcolumn}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Is Equal To":
                            $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                            $param = "{$filter['value']}";
                            break;
                        case "Starts With":
                            $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                            $param = "{$filter['value']}%";
                            break;
                        case "Ends With":
                            $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                            $param = "%{$filter['value']}";
                            break;
                        case "Is Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = array();
                            $psql = array();
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$pcolumn}_{$k}"] = "%{$p}%";
                                $psql[] = "{$column} LIKE :{$paramName}_{$pcolumn}_{$k}";
                            }
                            $sql = "(" . implode(" OR ", $psql) . ")";
                            break;
                        case "Is Not Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = array();
                            $psql = array();
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$pcolumn}_{$k}"] = "%{$p}%";
                                $psql[] = "{$column} NOT LIKE :{$paramName}_{$pcolumn}_{$k}";
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
                            $sql = "{$column} {$filter['operator']} :{$paramName}_{$pcolumn}";
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
                    case "Weekly":
                    case "Monthly":
                    case "Yearly":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $sql = "({$column} BETWEEN :{$paramName}_{$pcolumn}_from AND :{$paramName}_{$pcolumn}_to)";
                            $param = array(
                                ":{$paramName}_{$pcolumn}_from" => @$filter['value']['from'],
                                ":{$paramName}_{$pcolumn}_to" => @$filter['value']['to'],
                            );
                        }
                        break;
                    case "Not Between":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $sql = "({$column} NOT BETWEEN :{$paramName}_{$pcolumn}_from AND :{$paramName}_{$pcolumn}_to)";
                            $param = array(
                                ":{$paramName}_{$pcolumn}_from" => @$filter['value']['from'],
                                ":{$paramName}_{$pcolumn}_to" => @$filter['value']['to'],
                            );
                        }
                        break;
                    case "More Than":
                        if (@$filter['value']['from'] != '') {
                            $sql = "{$column} > :{$paramName}_{$pcolumn}";
                            $param = @$filter['value']['from'];
                        }
                        break;
                    case "Less Than":
                        if (@$filter['value']['to'] != '') {
                            $sql = "{$column} < :{$paramName}_{$pcolumn}";
                            $param = @$filter['value']['to'];
                        }
                        break;
                    case "Daily":
                        if (@$filter['value'] != '') {
                            $sql = "{$column} = DATE(:{$paramName}_{$pcolumn})";
                            $param = @$filter['value'];
                        }
                        break;
                }
                break;
            case "list":
                if ($filter['value'] != '') {
                    $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                    $param = @$filter['value'];
                }
                break;
            case "relation":
                if ($filter['value'] != '') {
                    $sql = "{$column} = :{$paramName}_{$pcolumn}";
                    $param = @$filter['value'];
                }
                break;
            case "check":
                if ($filter['value'] != '') {
                    $param = array();
                    $psql = array();
                    foreach ($filter['value'] as $k => $p) {
                        $param[":{$paramName}_{$pcolumn}_{$k}"] = "{$p}";
                        $psql[] = ":{$paramName}_{$pcolumn}_{$k}";
                    }
                    $sql = "{$column} IN (" . implode(", ", $psql) . ")";
                }
                break;
        }
        return array('sql' => $sql, 'param' => $param);
    }

    public static function generateParams($paramName, $params, $template = '', $paramOptions = array()) {
        $sql = array();
        $flatParams = array();

        $paramName = preg_replace('/[^\da-z]/i', '_', $paramName);

        if (is_array($params) && count($params) > 0) {
            foreach ($params as $column => $filter) {

                $param = DataFilter::buildSingleParam($paramName, $column, $filter);
                $sql[] = $param['sql'];
                if (is_array($param['param'])) {
                    foreach ($param['param'] as $key => $value) {
                        $flatParams[$key] = $value;
                    }
                } else {
                    $column = preg_replace('/[^\da-z]/i', '_', $column);
                    $flatParams[$paramName . "_" . $column] = $param['param'];
                }
            }
        }

        $query = '';
        if (count($sql) > 0) {
            $query = implode(" AND ", $sql);
            if (strpos("[{$paramName}]", $template) !== false) {
                $query = str_replace("[{$paramName}]", $query, $template);
            }

            if ($template == "[{$paramName}]" || $template == '') {
                $query = "where " . $query;
            }
        }

        $template = array(
            'sql' => $query,
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

            switch ($filter['filterType']) {
                case "list":
                case "check":
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
                    break;
                case "relation":
                    $rf = new RelationField;
                    $rf->params = $filter['relParams'];
                    $rf->modelClass = $filter['relModelClass'];
                    $rf->relationCriteria = $filter['relCriteria'];
                    $rf->idField = $filter['relIdField'];
                    $rf->labelField = $filter['relLabelField'];
                    $rf->builder = $this->builder;

                    $list = [];
                    $rawList = $rf->query('', $rf->params);
                    foreach ($rawList as $key => $val) {
                        $list[$val['value']] = $val['label'];
                    }

                    $this->filters[$k]['list'] = $list;
                    break;
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
