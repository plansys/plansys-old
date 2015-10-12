<?php

class GridView extends FormField {

    public static $toolbarName  = "GridView";
    public static $category     = "Data & Tables";
    public static $toolbarIcon  = "fa fa-table";
    public        $type         = 'Grid';
    public        $name         = '';
    public        $mode         = 'normal';
    public        $label        = '';
    public        $layout       = 'Vertical';
    public        $labelWidth   = 4;
    public        $fieldWidth   = 8;
    public        $options      = [];
    public        $tableOptions = [];
    public        $datasource   = '';
    public        $gridOptions  = [];
    public        $columns      = [];

    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function includeCSS() {
        return ['grid.css'];
    }

    public function includeJS() {
        return ['grid.js'];
    }

    public function render() {
        $this->processColumns();
        return $this->renderInternal('template_render.php');
    }

    private function processColumns() {
        foreach ($this->columns as $k => $c) {
            $this->columns[$k] = $this->processSingleColumn($c);
        }
    }

    private function processSingleColumn($c) {
        $name = explode(".", $c['name']);
        if (count($name) > 1) {
            $c['fieldName'] = $c['name'];
            $c['name']      = array_pop($name);
        }

        return $c;
    }

    public function actionCellTemplate() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $fb       = FormBuilder::load($post['class']);
        $field    = $fb->findField(['name' => $post['name']]);

        $this->attributes = $field;
        $post['item']     = $this->processSingleColumn($post['item']);
        echo $this->getRowTemplate($post['item'], $post['idx']);
    }

    public function getRowTemplate($col, $idx) {
        $template  = '';
        $fieldName = $col['name'];
        switch ($col['columnType']) {
            case "string":
                if (@$col['cellMode'] == 'custom' && trim(@$col['html']) != '') {
                    return @$col['html'];
                }

                $template = '{{row.' . $fieldName . '}}';
                break;
            case "checkbox":
                $template = '<label ng-if="row.$type == \'r\' || !row.$type"><input
ng-click="checkboxRow(row, \'' . $fieldName . '\', ' . $idx . ', $event)"
ng-checked="checkboxRowChecked(row, \'' . $fieldName . '\', ' . $idx . ')"
type="checkbox" /></label>';
                break;
        }

        $rowState = '';
        if ($idx == $this->getStartingColumnGroup()) {
            $rowState = "<div ng-include='\"row-state-template\"'></div>\n    ";
            $template = "<span class='row-group-padding'
        style='width:{{row.\$level*10}}px;'></span>
    {$template}";
        }


        return <<<EOF
<td ng-class="rowClass(row, '{$fieldName}', '{$col['columnType']}')">
    {$rowState}{$template}
</td>
EOF;
    }

    private function getStartingColumnGroup() {
        foreach ($this->columns as $k => $c) {
            if ($c['columnType'] == 'string') return $k;
        }
    }

    public function getGroupTemplate($col, $idx) {
        $template = '';
        switch ($col['columnType']) {
            case "string":
                $template = '<td style="cursor:pointer;" ng-click="hideGroup(row, $event)"></td>';

                break;
            case
            "checkbox":
                $template = <<<EOF
<td class="t-{$col['columnType']}"><label><input type="checkbox" class="cb-{$col['name']}"
ng-click="checkboxGroup(\$index, '{$col['name']}', '$idx', \$event)" /></label></td>
EOF;

                break;
        }

        if ($idx == $this->getStartingColumnGroup()) {
            $template = '
<td class="t-' . $col['columnType'] . '" ng-click="hideGroup(row, $event)">
    <div class="row-g" style="white-space:pre;cursor:pointer;"><span style="display:inline-block;width:{{row.$level*10}}px;float:"></span><i ng-if="!row.$hide" class="fa fa-caret-down"></i><i ng-if="!!row.$hide" class="fa fa-caret-right"></i>&nbsp; {{ row[row.$group] }}</div>
</td>
';
        }

        return $template;

    }

    public function getHeaderTemplate($col, $idx) {
        $fieldName = isset($col['fieldName']) ? $col['fieldName'] : $col['name'];

        switch ($col['columnType']) {
            case "string":
                $sortable = "ng-click=\"sort('{$fieldName}')\"";
                $caret    = <<<EOF
        <i class="fa fa-caret-up" ng-if="isSort('{$fieldName}', 'asc')"></i>
        <i class="fa fa-caret-down" ng-if="isSort('{$fieldName}', 'desc')"></i>
EOF;

                if (isset($col['options']) && isset($col['options']['sortable']) && $col['options']['sortable'] === 'false') {
                    $sortable = '';
                    $caret    = '';
                }

                return <<<EOF
<th><div class="th">
    <div class="row-header" {$sortable}>
        {$col['label']}
        {$caret}
    </div>
</div></th>
EOF;
                break;
            case "checkbox":
                return <<<EOL
<th><div class="th">
    <label><input type="checkbox"
ng-click="checkboxAll('{$col['name']}','{$idx}', \$event)" /></label>
</div></th>
EOL;
                break;
        }
    }

    public function getFieldProperties() {
        return array(
            array(
                'label' => 'GridView Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
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
                'type' => 'Text',
                'value' => '<div class=\'clearfix\'></div>',
            ),
            array(
                'label' => 'Grid Options',
                'name' => 'gridOptions',
                'type' => 'KeyValueGrid',
            ),
            array(
                'label' => 'Container Element Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array(
                'label' => 'Table Element Options',
                'name' => 'tableOptions',
                'type' => 'KeyValueGrid',
            ),
            array(
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array(
                'type' => 'Text',
                'value' => '<div style=\'margin-top:5px\'></div>',
            ),
            array(
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.GridViewCol',
                'inlineJS' => 'GridView/grid-builder.js',
                'options' => array(
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
                ),
                'singleViewOption' => array(
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array(
                        'ng-delay' => 500,
                    ),
                ),
                'type' => 'ListView',
            ),
        );
    }

}