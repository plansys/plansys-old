<?php

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
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
    public        $genOptions   = [];
    public        $columns      = [];
    public        $columnsFunc  = "";
    public        $columnsFuncParams = [];
    public        $hasEditable  = false;

    public function actionDownloadExcel() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        if (isset($post['rows'])) {
            $writer = WriterFactory::create(Type::XLSX);
            $dir = Yii::getPathOfAlias('root.assets.gvExport') ."/";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                chmod($dir, 0755);
            }
            $file = "export-". time() .".xlsx";
            $writer->openToFile($dir . $file); // stream data directly to the browser
            $writer->addRows($post['rows']); // add multiple rows at a time
            $writer->close();
            echo Yii::app()->baseUrl . '/assets/gvExport/' . $file;
        }
        
    }

    public function actionTemplate($n, $c, $k, $p) {
        $fb = FormBuilder::load($c);
        $ff = $fb->findField(['name' => $n]);
        $this->attributes = $ff;
        $this->prepareRender(json_decode($p, true));
        include("GridView/template_table.php");
    }

    public function actionEditHeader() {
        Asset::registerCSS(['application.components.ui.FormFields.GridView.grid-header-editor']);
        Yii::app()->controller->renderForm('GridViewHeader',null,[],[
            'layout'=>'//layouts/blank'
        ]);    
    }
    
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function includeCSS() {
        return ['grid.css'];
    }

    public function includeJS() {
        return ['grid.v1.js'];
    }

    public function prepareRender($overideParams = []) {
        $this->processColumns();
        
        if ($this->columnsFunc != '') {
            foreach ($this->columns as $idx => $col) {
                $this->getHeaderTemplate($this->columns[$idx], $idx, 'tag');
            }
            
            $params = [];
            foreach ($this->columnsFuncParams as $k=>$p) {
                if (strpos($p, 'php:') === 0) {
                    $params[$k] = $this->evaluateExpression($p, [
                        'columns' => $this->columns,
                        'data' => @$GLOBALS['dataSourceCache'][$this->datasource]
                    ]);
                }
            }
            
            foreach ($overideParams as $k=>$p) {
                $params[$k] = $p;
            }
            
            $this->columns = $this->evaluateExpression($this->columnsFunc, [
                'columns' => $this->columns,
                'params' => $params,
                'data' => @$GLOBALS['dataSourceCache'][$this->datasource]
            ]);
        }
        
        foreach ($this->columns as $k=>$c) {
            if (!isset($c['columnType'])) {
                $this->columns[$k]['columnType'] = 'string';
            }
        }
    }

    public function render() {
        $this->prepareRender();
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
        
        if (isset($c['options']['mode']) && strpos($c['options']['mode'], "editable") === 0) {
            $this->hasEditable = true;
        }
        
        $c['options']['sortable'] = false;

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
        $style     = '';
        $fieldName = $col['name'];
        if ($fieldName == "" && !@$col['options']['mode']) return "";

        $attr      = [];
        if (isset($col['options']['ng-if'])) {
            $attr['ng-if'] = $col['options']['ng-if'];
        }

        switch ($col['columnType']) {
            case "string":
                if (@$col['cellMode'] == 'custom' && trim(@$col['html']) != '') {
                    return @$col['html'];
                }

                $template = '{{row[\'' . $fieldName . '\']}}';
                break;
            case "checkbox":
                $ngif = "";
                if (isset($col['options']['ng-checkbox-if'])) {
                    $ngif = " && ({$col['options']['ng-checkbox-if']})";
                }
                
                $ngshow = "";
                if (isset($col['options']['ng-checkbox-show'])) {
                    $ngshow = "ng-show=\"{$col['options']['ng-checkbox-show']}\"";
                }
                
                if (isset($col['options']['ng-change'])) {
                    $col['options']['ng-checkbox-change'] = $col['options']['ng-change'];
                }
                
                $ngchange = "";
                if (isset($col['options']['ng-checkbox-change'])) {
                    $ngchange = "{$col['options']['ng-checkbox-change']};";
                }
                
                $ngdisabled = "";
                if (isset($col['options']['ng-checkbox-disabled'])) {
                    $ngdisabled = "ng-disabled=\"{$col['options']['ng-checkbox-disabled']};\"";
                }
                
                $template = '<label class="cbl-'.$fieldName.'" ng-if="(row.$type == \'r\' || !row.$type) '.$ngif.'" '.$ngshow.'><input
ng-click="checkboxRow(row, \'' . $fieldName . '\', ' . $idx . ', $event);'.$ngchange.'"
ng-checked="checkboxRowChecked(row, \'' . $fieldName . '\', ' . $idx . ')"
'.$ngdisabled.'
type="checkbox" /></label>';
                break;
        }

        $rowState = '';
        if ($idx == 0) {
            $rowStateCss = $this->hasEditable ? "class='editable'" : "";
            if (@$this->gridOptions['showRowState'] != 'false' && @$this->gridOptions['showRowStateBtn'] != 'false') {
                $rowState = "<div {$rowStateCss} ng-include='\"row-state-template\"'></div>\n    ";
            }
            if (!@$col['options']['mode'] && $col['columnType'] != 'checkbox') {
                $template = "<span class='row-group-padding' ng-if='!!row.\$level'
        style='width:{{row.\$level*10}}px;'></span>
    {$template}";
            }
        }


        $ngchange = '';
        if (isset($col['options']['ng-change'])) {
            $ngchange = ' ng-change="' . $col['options']['ng-change'] . '" ';
        }
        if (!!@$col['options']['mode']) {
            $editableCss = '';
            if ($idx == 0) {
                $editableCss = 'style="padding-right: 0px;padding-left: 8px;"';
            }

            switch ($col['options']['mode']) {
                case "html":
                    $template = '
    <div ng-bind-html="row[\'' . $fieldName . '\']></div>';
                    break;
                case "editable":
                    $template = '
    <div ceditable="true" ' . $editableCss . $ngchange . ' ng-paste="paste($event, row, $index, \''.$fieldName.'\', '.$idx.')" ng-model="row[\'' . $fieldName . '\']"
         ng-keydown="editKey($event)"></div>';
                    break;
                case "editable-insert":
                    $template = '
    <div ceditable="true" ' . $editableCss . $ngchange . ' ng-if="row.$rowState == \'insert\'"
         ng-model="row[\'' . $fieldName . '\'] ng-keydown="editKey($event)"></div>
    <span ng-show="row.$rowState != \'insert\'">' . $template . '</span>';
                    break;
                case "editable-update":
                    $template = '
    <div ceditable="true" ' . $editableCss . $ngchange . ' ng-if="row.$rowState != \'insert\'"
         ng-model="row[\'' . $fieldName . '\'] ng-keydown="editKey($event)"></div>
    <span ng-show="row.$rowState == \'insert\'">' . $template . '</span>';
                    break;
                case "del-button":
                    if (!isset($col['options']['delUrl'])) {
                        $style    = ' style="width:20px;"';
                        $template = '<div ng-if="!row.$rowState" ng-click="removeRow(row)" title="Remove" 
    class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></div>

    <div ng-if="(!row.$type || row.$type === \'r\') && [\'edit\',\'remove\'].indexOf(row.$rowState) >= 0" ng-click="undoRemoveRow(row)" title="Undo Remove" 
         class="btn btn-default btn-xs"><i class="fa fa-undo"></i></div>';
                    } else {
                        $style    = ' style="width:20px;"';
                        $template = '<a ng-if="(!row.$type || row.$type === \'r\')" ng-url="' . $col['options']['delUrl'] . '"
    onClick="return confirm(\'Are you sure?\')"
    class="btn-block btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                    }
                    break;
                case "unchoose-button":
                    $style    = ' style="width:20px;"';
                    $template = '<div ng-if="(!row.$type || row.$type === \'r\') && (!row.$rowState || row.$rowState == \'insert\')" ng-click="removeRow(row)"
    class="btn btn-danger btn-xs"><i class="fa fa-times"></i></div>

    <div ng-if="[\'remove\'].indexOf(row.$rowState) >= 0" ng-click="undoRemoveRow(row)" title="Remove"
    class="btn btn-default btn-xs"><i class="fa fa-undo"></i></div>
    ';
                    break;
                case 'edit-popup-button':
                    $style    = ' style="width:20px;"';
                    $template = '<a ng-if="row.$rowState != \'insert\'" ng-click="
'.$col['options']['popupName'].'.editId = row[datasource.primaryKey];
' . $col['options']['popupName'] . '.open()"
    class="btn-block btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>';
                    break;
                case 'edit-button':
                    $style    = ' style="width:20px;"';
                    $template = '<a ng-if="(!row.$type || row.$type === \'r\')" ng-url="' . $col['options']['editUrl'] . '" title="Update" 
    class="btn-block btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>';
                    break;
                case 'sequence':
                    $style    = ' style="width:20px;"';
                    $template = '{{ getSequence(row, $index + 1); }}';
                    break;
                case 'date':
                    $template = '{{row[\'' . $fieldName . '\'] | dateFormat:"date" }}';
                    break;
                case 'time':
                    $template = '{{row[\'' . $fieldName . '\'] | dateFormat:"time" }}';
                    break;
                case 'datetime':
                    $template = '{{row[\'' . $fieldName . '\'] | dateFormat:"datetime" }}';
                    break;
            }
        }
        
        $width = $this->getWidth($col);
        if ($width != '') {
            $attr['style'] = $width;
        }
        
        if (is_array($attr)) {
            $attr = $this->expandAttributes($attr);
        }

        return <<<EOF
<td{$style} ng-class="rowClass(row, '{$fieldName}', '{$col['columnType']}')" {$attr}>
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

        $attr      = [];
        if (isset($col['options']['ng-if'])) {
            $attr['ng-if'] = $col['options']['ng-if'];
        }
        $width = $this->getWidth($col, false);
        if ($width != '') {
            $attr['style'] = $width;
        }
        if (is_array($attr)) {
            $attr = $this->expandAttributes($attr);
        }

        if ($idx == $this->getStartingColumnGroup()) {
            $template = '
<td class="t-' . $col['columnType'] . '" ng-click="hideGroup(row, $event)" '.$attr.'>
    <div class="row-g" style="white-space:pre;cursor:pointer;"><span style="display:inline-block;width:{{row.$level*10}}px;float:"></span><i ng-if="!row.$hide" class="fa fa-caret-down"></i><i ng-if="!!row.$hide" class="fa fa-caret-right"></i>&nbsp; {{ row[row.$group] }}</div>
</td>
';
        }
        return $template;
    }

    public function generateHeaders($mode, $cols = false) {
        $rowHeaders = isset($this->gridOptions['rowHeaders']) ? $this->gridOptions['rowHeaders'] * 1: 1; 
        $cols = !$cols ? $this->columns : $cols;
        $rowSpan = [];
        ob_start();
        for($i = $rowHeaders; $i >=1; $i--) {
            echo ($mode== 'class' ? '<div class="tr">' : '<tr>'); 
            foreach ($cols as $idx => $col) {
                if (@$col['headers']['hide'] == true) {
                    continue;
                }
                if (@$col['headers']['r' . $i]['rowSpan'] > 1) {
                    $rowSpan[$idx] = ['current'=>$i,'total'=>$rowHeaders];
                }
                if (isset($rowSpan[$idx])) {
                    $col['rowSpan'] = $rowSpan[$idx];
                }
                
                $cidx = isset($col['idx']) ? $col['idx'] : $idx;
                if ($i == 1) {
                    if (@$col['headers']['r1']['colSpan'] > 1) {
                        echo $this->getSuperHeaderTemplate($i, $col, $cidx, $mode);
                    } else {
                        echo $this->getHeaderTemplate($col, $cidx, $mode);
                    }
                } else {
                    echo $this->getSuperHeaderTemplate($i, $col, $cidx, $mode);
                }
            }
            echo ($mode == 'class' ? '</div>' : '</tr>');
        }
        return ob_get_clean();
    }
    
    public function getFreezedCols() {
        $cols = [];
        foreach ($this->columns as $idx=>$col) {
            if (@$col['options']['freeze'] == 'true') {
                $col['idx'] = $idx;
                $cols[] = $col;
            }
        }
        return $cols;
    }
    
    private function getWidth($col, $overflow = true) {
        if (@$col['options']['width'] != "") {
            $style = 'width:' .$col['options']['width'] . 'px;';
            $style .= 'min-width:' .$col['options']['width'] . 'px;';
            $style .= 'max-width:' .$col['options']['width'] . 'px;';
            if ($overflow) {
                $style .= 'overflow-x: hidden;';
            }
            return $style;
        }
        
        return '';
    }
    
    public function getSuperHeaderTemplate($row, $col, $idx, $mode) {
        $attr      = [];
        $attr['cidx'] = $idx;
        $attr['ridx'] = $row;
        
        if (isset($col['options']['ng-if'])) {
            $attr['ng-if'] = $col['options']['ng-if'];
        }
        
        if (@$col['options']['freeze'] === 'true') {
            $attr['freeze'] = 'true';
        }
        
        if (!isset($col['headers'])) {
            $col['headers'] = [];
        }
        
        if (!isset($col['headers']['r'. $row])) {
            $col['headers']['r'. $row] = [
                'label' => '',
                'colSpan' => '1'
            ];
        }
        
        $headers = $col['headers']['r'. $row];
        $width = $this->getWidth($col);
        if ($width != '') {
            $attr['style'] = $width;
        }
        
        if (isset($col['rowSpan'])) {
            if ($col['rowSpan']['current'] == $row) {
                $attr['rowspan'] = $col['rowSpan']['total'];
            } else {
                return "";
            }
        }
        
        if ($headers['colSpan'] < 1) {
            return "";
        } else if ($headers['colSpan'] > 1) {
            $attr['colspan'] = $headers['colSpan']; 
        }
        if (is_array($attr)) {
            $attr = $this->expandAttributes($attr);
        }
        
        if ($row > 1) {
            $content = $headers['label'];
        } else {
            $content = $col['label'];
        }
        if ($mode == 'class') {
            return <<<EOL
<div class="th" {$attr}>
{$content}
</div>
EOL;
        } else {
            return <<<EOL
<th {$attr}>
{$content}
</th>
EOL;
        }
    }

    public function getHeaderTemplate(&$col, $idx, $mode) {
        $fieldName = isset($col['fieldName']) ? $col['fieldName'] : $col['name'];
        if ($fieldName == "" && !@$col['options']['mode']) return "";

        $attr      = [];
        $attr['cidx'] = $idx;
        $attr['ridx'] = 1;
        if (@$col['options']['ng-if'] != "") {
            $attr['ng-if'] = $col['options']['ng-if'];
        }
        
        if (@$col['options']['freeze'] === 'true') {
            $attr['freeze'] = 'true';
        }
        
        $width = $this->getWidth($col);
        if ($width != '') {
            $attr['style'] = $width;
        }
        
        if (isset($col['rowSpan'])) {
            return "";
        }
        
        if (is_array($attr)) {
            $attr = $this->expandAttributes($attr);
        }
        
        
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

                 $content = <<<EOF
    <div class="row-header" {$sortable}>
        {$col['label']}
        {$caret}
    </div>
EOF;
                break;
            case "checkbox":
                $ngif = "";
                if (isset($col['options']['ng-checkbox-head-if'])) {
                    $ngif = "ng-if=\"({$col['options']['ng-checkbox-head-if']})\"";
                }
                
                $content =  <<<EOL
    <label {$ngif}><input type="checkbox"
ng-click="checkboxAll('{$col['name']}','{$idx}', \$event)" class="cb-th-{$col['name']}" /></label>
EOL;

                
                break;
        }
        
        if (isset($col['labelHtml'])) {
            $content = $col['labelHtml'];
        } else {
            // $col['labelHtml'] = $content;
        }
        
        if ($mode == 'class') {
            return <<<EOL
<div class="th" {$attr}>
{$content}
</div>
EOL;
        } else {
            return <<<EOL
<th {$attr}>
{$content}
</th>
EOL;
        }
        
    }

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'GridView Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
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
                'type' => 'Text',
                'value' => '<div class=\'clearfix\'></div>',
            ),
            array (
                'label' => 'Grid Options',
                'name' => 'gridOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Container Element Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Table Element Options',
                'name' => 'tableOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Columns Function',
                'fieldname' => 'columnsFunc',
                'desc' => 'Use $columns and $data to get columns and data.<br/>
 e.g: YourClass::function($columns, $params, $data)
<br/><br/>
NOTE: to use $data, you must set \'cache\' = \'true\' <br/>
in your DataSource Options',
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Columns Function Params',
                'name' => 'columnsFuncParams',
                'options' => array (
                    'ng-if' => '!!active.columnsFunc',
                ),
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Header',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-click=\"headerPopUp.open()\" 
     style=\'margin-top:-25px\'
     class=\"btn btn-xs pull-right btn-info\">
    <b><i class=\"fa fa-pencil\"></i> Edit Header</b>
</div>',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'headerPopUp',
                'options' => array (
                    'width' => '800',
                    'height' => '400',
                ),
                'mode' => 'url',
                'subForm' => 'application.components.ui.FormFields.GridViewHeader',
                'url' => '/formfield/GridView.editHeader',
                'title' => 'GridView Header Setting',
                'parentForm' => 'application.components.ui.FormFields.GridView',
            ),
            array (
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Generate Columns',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 0px 0px 0px;',
                    'ng-show' => 'active.datasource != \'\'',
                    'ng-click' => 'generateColumns()',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\'margin-top:5px\'></div>',
            ),
            array (
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.GridViewCol',
                'inlineJS' => 'GridView/grid-builder.js',
                'options' => array (
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
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

}