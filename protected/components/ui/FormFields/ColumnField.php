<?php

class ColumnField extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property ColumnField.
	 */
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
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            '<hr/>',
        );
    }
	
	/** @var integer variable untuk menampung jumlah columns dengan default 2 */
    public $totalColumns = 2;
	
	/** @var string variable untuk menampung kondisi border dengan default No */
    public $showBorder = 'No';
	
	/** @var array variable untuk menampung parseField */
    public $parseField = array(
        'column1' => 'renderColumn1',
        'column2' => 'renderColumn2',
        'column3' => 'renderColumn3',
        'column4' => 'renderColumn4',
        'column5' => 'renderColumn5',
    );
	
	/** @var array variable untuk menampung DIV column */
    public $column1 = array('<column-placeholder></column-placeholder>');
	
	/** @var array variable untuk menampung DIV column */
    public $column2 = array('<column-placeholder></column-placeholder>');
	
	/** @var array variable untuk menampung DIV column */
    public $column3 = array('<column-placeholder></column-placeholder>');
	
	/** @var array variable untuk menampung DIV column */
    public $column4 = array('<column-placeholder></column-placeholder>');
	
	/** @var array variable untuk menampung DIV column */
    public $column5 = array('<column-placeholder></column-placeholder>');
	
	/** @var string variable yang digunakan pada saat renderColumn */
    public $renderColumn1 = "";
	
	/** @var string variable yang digunakan pada saat renderColumn */
    public $renderColumn2 = "";
	
	/** @var string variable yang digunakan pada saat renderColumn */
    public $renderColumn3 = "";
	
	/** @var string variable yang digunakan pada saat renderColumn */
    public $renderColumn4 = "";
	
	/** @var string variable yang digunakan pada saat renderColumn */
    public $renderColumn5 = "";
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "Columns";
	
	/** @var string variable untuk menampung category */
    public static $category = "Layout";
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-columns";
	
	/**
	 * @return integer Fungsi ini akan me-return width dari column yang akan dirender.
	*/
    public function getColumnWidth() {
        if (is_array($this->totalColumns)) {
            return 100 / count($this->totalColumns);
        } else {
            return 100 / $this->totalColumns;
        }
    }

	/**
	 * @param integer $i Parameter untuk melempar jumlah column yang di-render.
	 * @return html Fungsi ini untuk me-render column.
	*/
    public function renderColumn($i) {
        $column = 'renderColumn' . $i;

        $html = $this->$column;
        if (trim($html == "<column-placeholder></column-placeholder>")) {
            $html = "&nbsp;";
        }

        return $html;
    }

	/**
	 * @return fields Fungsi ini untuk me-render field dan atributnya.
	*/
    public function render() {
        return $this->renderInternal('template_render.php');
    }

}
