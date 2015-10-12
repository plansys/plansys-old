<?php

/**
 * Class ColumnField
 * @author rizky
 */
class ColumnField extends FormField {

    public static $toolbarName      = "Columns";
    public static $category         = "Layout";
    public static $toolbarIcon      = "fa fa-columns";
    public        $totalColumns     = 2;
    public        $showBorder       = 'No';
    public        $parseField       = [
        'column1' => 'renderColumn1',
        'column2' => 'renderColumn2',
        'column3' => 'renderColumn3',
        'column4' => 'renderColumn4',
        'column5' => 'renderColumn5',
    ];
    public        $column1          = ['<column-placeholder></column-placeholder>'];
    public        $column2          = ['<column-placeholder></column-placeholder>'];
    public        $column3          = ['<column-placeholder></column-placeholder>'];
    public        $column4          = ['<column-placeholder></column-placeholder>'];
    public        $column5          = ['<column-placeholder></column-placeholder>'];
    public        $renderColumn1    = "";
    public        $renderColumn2    = "";
    public        $renderColumn3    = "";
    public        $renderColumn4    = "";
    public        $renderColumn5    = "";
    public        $w1;
    public        $w2;
    public        $w3;
    public        $w4;
    public        $w5;
    public        $options          = [];
    public        $perColumnOptions = [];

    /**
     * @return array me-return array property ColumnField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Total Columns',
                'name' => 'totalColumns',
                'options' => array (
                    'ng-change' => 'activeEditor.changeTC();',
                    'ng-model' => 'active.totalColumns',
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
                'listExpr' => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Width Column 1',
                'name' => 'w1',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-if' => 'active.totalColumns >= 1',
                    'ng-model' => 'active.w1',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Width Column 2',
                'name' => 'w2',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-if' => 'active.totalColumns >= 2',
                    'ng-model' => 'active.w2',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Width Column 3',
                'name' => 'w3',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-if' => 'active.totalColumns >= 3',
                    'ng-model' => 'active.w3',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Width Column 4',
                'name' => 'w4',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-if' => 'active.totalColumns >= 4',
                    'ng-model' => 'active.w4',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Width Column 5',
                'name' => 'w5',
                'fieldWidth' => '4',
                'options' => array (
                    'ng-if' => 'active.totalColumns >= 5',
                    'ng-model' => 'active.w5',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Container Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Per Column Options',
                'name' => 'perColumnOptions',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /**
     * @return integer me-return width dari column yang akan dirender.
     */
    public function getColumnWidth() {
        if (is_array($this->totalColumns)) {
            return 100 / count($this->totalColumns);
        } else {
            return 100 / $this->totalColumns;
        }
    }

    /**
     * @param integer $i column berapa yang di-render
     * @return string me-return string berisi tag html
     */
    public function renderColumn($i) {
        $column = 'renderColumn' . $i;

        $html = $this->$column;
        if (trim($html == "<column-placeholder></column-placeholder>")) {
            $html = "&nbsp;";
        }

        return $html;
    }

    public function includeEditorJS() {
        return ['column-field-editor.js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field ColumnField dari hasil render
     */
    public function render() {

        $defaultWidth = round(100 / $this->totalColumns);
        for ($i = 1; $i <= $this->totalColumns; $i++) {
            $this->{'w' . $i} = !isset($this->{'w' . $i}) ? $defaultWidth . "%" : $this->{'w' . $i};
        }

        $this->addClass('column-field', 'options');
        return $this->renderInternal('template_render.php');
    }

}