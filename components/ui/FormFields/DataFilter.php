<?php

/**
 * Class DataFilter
 * @author rizky
 */
class DataFilter extends FormField {

    /** @var string $toolbarName */
    public static $toolbarName = "Data Filter";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-filter";

    /** @var string $name */
    public $name;

    /** @var string $datasource */
    public $datasource;

    /** @var string $filters */
    public $filters = [];
    public $options = [];
    public $includeEmpty = 'No';
    public $emptyValue = '';
    public $emptyLabel = '';
    public $filterOperators = [
        'string' => [
            'Is Any Of',
            'Is Not Any Of',
            'Contains',
            'Does Not Contain',
            'Is Equal To',
            'Starts With',
            'Ends With',
            'Is Empty',
            'Is Not Empty'
        ],
        'number' => [
            '=',
            '<>',
            '>',
            '>=',
            '<=',
            '<',
            'Is Empty',
            'Is Not Empty'
        ],
        'date' => [
            'Between',
            'Not Between',
            'Less Than',
            'More Than',
            'Daily',
            'Weekly',
            'Monthly',
            'Yearly',
        ]
    ];

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
        return ['' => 'No Operator'] + $result;
    }

    public static function generateParams($paramName, $params, $template = '', $paramOptions = []) {
        $sql = [];
        $flatParams = [];
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

        $template = [
            'sql' => $query,
            'params' => $flatParams
        ];

        return $template;
    }

    public static function toSQLDateTime($val, $driver = null) {
        if (is_null($driver)) {
            $driver = Setting::get('db.driver');
        }

        switch ($driver) {
            case "oci";
                return "TO_DATE({$val}, 'YYYY-MM-DD HH24:MI:SS')";
                break;
            default:
                return $val;
                break;
        }
    }


    public static function toSQLStr($val, $driver = null) {
        if (is_null($driver)) {
            $driver = Setting::get('db.driver');
        }

        switch ($driver) {
            case "oci";
                return "UPPER({$val})";
                break;
            default:
                return $val;
                break;
        }
    }

    public static function toSQLDate($val, $driver = null) {
        if (is_null($driver)) {
            $driver = Setting::get('db.driver');
        }

        switch ($driver) {
            case "oci";
                return "TO_DATE({$val}, 'YYYY-MM-DD')";
                break;
            default:
                return $val;
                break;
        }
    }

    protected static function buildSingleParam($paramName, $column, $filter) {
        $sql = "";
        $param = "";
        $pcolumn = preg_replace('/[^\da-z]/i', '_', $column);
        $driver = Setting::get('db.driver');
        
        
        if (@$filter['mode'] == 'raw' && isset($filter['colname'])) {
            $column = $filter['colname'];
        } else {
            $column = ActiveRecord::formatSingleCriteria($column, $driver);
            
            ## quote field if it is containing illegal char
            if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", str_replace(".", "", $column))) {
                $column = "{$column}";
            }
        }

        switch ($filter['type']) {
            case "string":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    $sCol = DataFilter::toSQLStr("{$column}", $driver);
                    $spCol = DataFilter::toSQLStr(":{$paramName}_{$pcolumn}", $driver);

                    switch ($filter['operator']) {
                        case "Contains":
                            $sql = "{$sCol} LIKE {$spCol}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Does Not Contain":
                            $sql = "{$sCol} NOT LIKE {$spCol}";
                            $param = "%{$filter['value']}%";
                            break;
                        case "Is Equal To":
                            $sql = "{$sCol} LIKE {$spCol}";
                            $param = "{$filter['value']}";
                            break;
                        case "Starts With":
                            $sql = "{$sCol} LIKE {$spCol}";
                            $param = "{$filter['value']}%";
                            break;
                        case "Ends With":
                            $sql = "{$sCol} LIKE {$spCol}";
                            $param = "%{$filter['value']}";
                            break;
                        case "Is Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = [];
                            $psql = [];
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$pcolumn}_{$k}"] = "%{$p}%";
                                $spCol = DataFilter::toSQLStr(":{$paramName}_{$pcolumn}_{$k}", $driver);
                                $psql[] = "{$sCol} LIKE {$spCol}";
                            }
                            $sql = "(" . implode(" OR ", $psql) . ")";
                            break;
                        case "Is Not Any Of":
                            $param_raw = preg_split('/\s+/', trim($filter['value']));
                            $param = [];
                            $psql = [];
                            foreach ($param_raw as $k => $p) {
                                $param[":{$paramName}_{$pcolumn}_{$k}"] = "%{$p}%";
                                $spCol = DataFilter::toSQLStr(":{$paramName}_{$pcolumn}_{$k}", $driver);
                                $psql[] = "{$sCol} LIKE {$spCol}";
                            }
                            $sql = "(" . implode(" AND ", $psql) . ")";
                            break;
                        case "Is Empty":
                            $sql = "({$column} LIKE '' OR {$column} IS NULL)";
                            break;
                        case "Is Not Empty":
                            $sql = "({$column} NOT LIKE '' AND {$column} IS NOT NULL)";
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
                        case "Is Not Empty":
                            $sql = "{$column} IS NOT NULL)";
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
                            $a = self::toSQLDateTime(":{$paramName}_{$pcolumn}_from", $driver);
                            $b = self::toSQLDateTime(":{$paramName}_{$pcolumn}_to", $driver);

                            $sql = "({$column} BETWEEN {$a} AND {$b})";
                            $fromStartHour = date('Y-m-d 23:59:00', strtotime('-1 day', strtotime(@$filter['value']['from'])));
                            $toLastHour = date('Y-m-d 23:59:00', strtotime(@$filter['value']['to']));

                            $param = [
                                ":{$paramName}_{$pcolumn}_from" => $fromStartHour,
                                ":{$paramName}_{$pcolumn}_to" => $toLastHour,
                            ];
                        }
                        break;
                    case "Not Between":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $a = self::toSQLDateTime(":{$paramName}_{$pcolumn}_from", $driver);
                            $b = self::toSQLDateTime(":{$paramName}_{$pcolumn}_to", $driver);

                            $sql = "({$column} NOT BETWEEN {$a} AND {$b})";

                            $toLastHour = date('Y-m-d 23:59:00', strtotime(@$filter['value']['to']));
                            $param = [
                                ":{$paramName}_{$pcolumn}_from" => @$filter['value']['from'],
                                ":{$paramName}_{$pcolumn}_to" => $toLastHour,
                            ];

                            if (@$filter['value']['to'] == '' || @$filter['value']['from'] == '') {
                                $sql = "1 = 1";
                            }
                        }
                        break;
                    case "More Than":
                        if (@$filter['value']['from'] != '') {
                            $sql = "{$column} > " . self::toSQLDateTime(":{$paramName}_{$pcolumn}", $driver);
                            $param = @$filter['value']['from'];
                        }
                        break;
                    case "Less Than":
                        if (@$filter['value']['to'] != '') {
                            $sql = "{$column} < " . self::toSQLDateTime(":{$paramName}_{$pcolumn}", $driver);
                            $param = @$filter['value']['to'];
                        }
                        break;
                    case "Daily":
                        if (@$filter['value'] != '') {
                            if ($driver == "mysql") {
                                $sql = "DATE({$column}) = DATE(:{$paramName}_{$pcolumn})";
                            } else if ($driver == "oci") {
                                $sql = "TO_CHAR({$column},'YY-MM-DD') = TO_CHAR(" . self::toSQLDateTime(":{$paramName}_{$pcolumn}", $driver) . ", 'YY-MM-DD')";
                            }
                            $param = @$filter['value'];
                        }
                        break;
                }
                break;
            case "list":
                if (isset($filter['value']) && $filter['value'] != '') {
                    $sql = "{$column} LIKE :{$paramName}_{$pcolumn}";
                    $param = @$filter['value'];
                }
                break;
            case "relation":
                switch ($filter['operator']) {
                    case 'empty':
                        if ($filter['value'] == 'null') {
                            $sql = "{$column} is null";
                            $param = @$filter['value'];
                        } else {
                            $sql = "{$column} = :{$paramName}_{$pcolumn}";
                            $param = @$filter['value'];
                        }
                        break;
                    default:
                        if ($filter['value'] != '') {
                            $sql = "{$column} = :{$paramName}_{$pcolumn}";
                            $param = @$filter['value'];
                        }
                        break;
                }
                break;
            case "check":
                if ($filter['value'] != '') {
                    if (@$filter['operator'] == 'in') {
                        // USING IN...
                        $param = [];
                        $psql = [];
                        foreach ($filter['value'] as $k => $p) {
                            $param[":{$paramName}_{$pcolumn}_{$k}"] = "{$p}";
                            $psql[] = ":{$paramName}_{$pcolumn}_{$k}";
                        }
                        $sql = "{$column} IN (" . implode(", ", $psql) . ")";
                    } else {
                        // USING LIKE...
                        $param = [];
                        $psql = [];
                        foreach ($filter['value'] as $k => $p) {
                            $param[":{$paramName}_{$pcolumn}_{$k}"] = "%{$p}%";
                            $psql[] = "{$column} LIKE :{$paramName}_{$pcolumn}_{$k}";
                        }
                        $sql = "(" . implode(" AND ", $psql) . ")";
                    }
                }
                break;
        }
        return ['sql' => $sql, 'param' => $param];
    }

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
            array (
                'type' => 'Text',
                'value' => '<div class=\'clearfix\'></div>',
            ),
            array (
                'label' => 'DataFilter Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Filters',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\'margin-top:5px;\'></div>',
            ),
            array (
                'name' => 'filters',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataFilterListForm',
                'inlineJS' => 'DataFilter/inlinejs/dfr-init.js',
                'options' => array (
                    'ng-model' => 'active.filters',
                    'ng-change' => 'save()',
                    'ps-after-add' => 'value.show = true;',
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
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['data-filter.js'];
    }

    public function includeCSS() {
        return ['data-filter.css'];
    }

    public function datasources() {
        $ds = $this->builder->findAllField(['type' => 'DataSource']);
        $return = [];
        foreach ($ds as $d) {
            if (@$d['params']['where'] == $this->name || $d['name'] == $this->datasource) {
                array_push($return, $d['name']);
            }
        }

        return $return;
    }

    public function actionRelInit() {

        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);

        if (count($post) == 0)
            die();

        $fb = FormBuilder::load($post['m']);
        $ff = $fb->findField(['name' => $post['f']]);
        
        foreach ($ff['filters'] as $filter) {
            if ($filter['name'] != $post['n'])
                continue;
                
            if (!isset($filter['relParams']) 
                || !isset($filter['relModelClass']) 
                || !isset($filter['relCriteria'])) 
                continue;

            $rf = new RelationField;
            $rf->params = $filter['relParams'];
            $rf->modelClass = $filter['relModelClass'];
            $rf->relationCriteria = $filter['relCriteria'];
            $rf->relationCriteria['limit'] = ActiveRecord::DEFAULT_PAGE_SIZE;
            $rf->relationCriteria['offset'] = 0;

            $rf->idField = $filter['relIdField'];
            $rf->labelField = $filter['relLabelField'];

            if (isset($post['v'])) {
                $rf->relationCriteria['condition'] = $rf->idField . ' = :dataFilterID';
                $rf->params[':dataFilterID'] = $post['v'];
            }
            $rf->builder = $this->builder;

            $rawList = $rf->query(@$post['s'], $rf->params);
            echo json_encode($rawList);
        }
    }

    public function actionRelnext() {

        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);

        if (count($post) == 0) {
            die();
        }

        $start = @$post['i'];

        $fb = FormBuilder::load($post['m']);
        $ff = $fb->findField(['name' => $post['f']]);

        foreach ($ff['filters'] as $filter) {
            if ($filter['name'] != $post['n'])
                continue;

            $rf = new RelationField;
            $rf->params = $filter['relParams'];
            $rf->modelClass = $filter['relModelClass'];
            $rf->relationCriteria = $filter['relCriteria'];
            $rf->relationCriteria['limit'] = ActiveRecord::DEFAULT_PAGE_SIZE;
            $rf->relationCriteria['offset'] = $start;

            $rf->idField = $filter['relIdField'];
            $rf->labelField = $filter['relLabelField'];
            $rf->builder = $this->builder;

            $rf->params = is_null($filter['relParams']) ? [] : $filter['relParams'];
            if (is_array($rf->params)) {
                foreach ($rf->params as $k => $ff) {
                    if (substr($ff, 0, 3) == "js:" && isset($post['p'][$k])) {
                        $rf->params[$k] = "'" . @$post['p'][$k] . "'";
                    }
                }
            }

            $list = [];
            $rawList = $rf->query(@$post['s'], $rf->params);
            $rawList = is_null($rawList) ? [] : $rawList;

            foreach ($rawList as $key => $val) {
                $list[] = [
                    'key' => $val['value'],
                    'value' => $val['label']
                ];
            }

            $count = $rf->count(@$post['s'], $rf->params);

            echo json_encode([
                'list' => $list,
                'count' => $count,
                's' => $post['s']
            ]);
        }
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut datafilter dari hasil render
     */
    public function render() {
        $this->processExpr(true);
        return $this->renderInternal('template_render.php');
    }

    /**
     * @return array me-return array hasil proses expression.
     */
    public function processExpr($fromRender = false) {
        if (count($this->filters) == 0)
            return [];

        foreach ($this->filters as $k => $filter) {
            switch ($filter['filterType']) {
                case "list":
                case "check":
                    if (isset($filter['listExpr']) && trim($filter['listExpr']) != '') {
                        ## evaluate expression
                        $list = $this->evaluate($filter['listExpr'], true);
                        
                        ## kalau listExpr ini berisi html, nanti bakal menghancurkan layout
                        ## karena html nya itu masuk ke dalam json, dan bakal di render
                        ## jadinya kita unset saja setelah di proses biar ga masuk ke json
                    
                        ## change sequential array to associative array
                        if (is_array($list) && !Helper::is_assoc($list)) {
                            if (!isset($list[0]['key'])) {
                                $list = Helper::toAssoc($list);
                            }
                        }
                        
                        foreach ($list as $i=>$listItem) {
                            if (is_array($listItem)) {
                                foreach ($listItem as $l => $listSubItem) {
                                    $list[$i][$l] = CHtml::encode($listSubItem);
                                }
                            } else {
                                $list[$i] = CHtml::encode($listItem);
                            }
                        }
                        
                        // if ($fromRender) {
                        //     $this->filters[$k]['listExpr'] = "";
                        // }
                        $this->filters[$k]['list'] = $list;
                    } 
                    break;
                case "relation":
                    $rf = new RelationField;
                    $rf->params = @$filter['relParams'];
                    $rf->modelClass = @$filter['relModelClass'];
                    $rf->relationCriteria = @$filter['relCriteria'];
                    $rf->relationCriteria['limit'] = ActiveRecord::DEFAULT_PAGE_SIZE;
                    $rf->relationCriteria['offset'] = 0;

                    $rf->idField = @$filter['relIdField'];
                    $rf->labelField = @$filter['relLabelField'];
                    $rf->builder = $this->builder;

                    $this->filters[$k]['list'] = 0;
                    $this->filters[$k]['count'] = 0;
                    break;
            }
        }

        return [
            'filters' => $this->filters
        ];
    }

}