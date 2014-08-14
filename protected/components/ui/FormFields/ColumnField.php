<?php

class ColumnField extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Total Columns',
                'name' => 'totalColumns',
                'options' => array (
                    'ng-change' => 'save()',
                    'ng-model' => 'active.totalColumns',
                ),
                'list' => array (
                    '1 Column',
                    '2 Columns',
                    '3 Columns',
                    '4 Columns',
                    '5 Columns',
                ),
                'listExpr' => 'array(
   \'1\'=>\'1 Column\',
   \'2\'=>\'2 Columns\',
   \'3\'=>\'3 Columns\',
   \'4\'=>\'4 Columns\',
   \'5\'=>\'5 Columns\'
)',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show Border',
                'name' => 'showBorder',
                'options' => array (
                    'ng-model' => 'active.showBorder',
                    'ng-change' => 'save();relayout();',
                ),
                'list' => array (
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
        );
    }

    public $totalColumns = 2;
    public $showBorder = 'No';
    public $parseField = array(
        'column1' => 'renderColumn1',
        'column2' => 'renderColumn2',
        'column3' => 'renderColumn3',
        'column4' => 'renderColumn4',
        'column5' => 'renderColumn5',
    );
    public $column1 = array('<column-placeholder></column-placeholder>');
    public $column2 = array('<column-placeholder></column-placeholder>');
    public $column3 = array('<column-placeholder></column-placeholder>');
    public $column4 = array('<column-placeholder></column-placeholder>');
    public $column5 = array('<column-placeholder></column-placeholder>');
    public $renderColumn1 = "";
    public $renderColumn2 = "";
    public $renderColumn3 = "";
    public $renderColumn4 = "";
    public $renderColumn5 = "";
    public static $toolbarName = "Columns";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-columns";

    public function getColumnWidth() {
        if (is_array($this->totalColumns)) {
            return 100 / count($this->totalColumns);
        } else {
            return 100 / $this->totalColumns;
        }
    }

    public function renderColumn($i) {
        $column = 'renderColumn' . $i;

        $html = $this->$column;
        if (trim($html == "<column-placeholder></column-placeholder>")) {
            $html = "&nbsp;";
        }

        return $html;
    }

    public function render() {
        return $this->renderInternal('template_render.php');
    }

}
