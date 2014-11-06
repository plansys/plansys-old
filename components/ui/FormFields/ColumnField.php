<?php
/**
 * Class ColumnField
 * @author rizky
 */
class ColumnField extends FormField {
    /**
     * @return array me-return array property ColumnField.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Total Columns',
                'name' => 'totalColumns',
                'options' =>  [
                    'ng-change' => 'save()',
                    'ng-model' => 'active.totalColumns',
                ],
                'listExpr' => 'array(
   \'1\'=>\'1 Column\',
   \'2\'=>\'2 Columns\',
   \'3\'=>\'3 Columns\',
   \'4\'=>\'4 Columns\',
   \'5\'=>\'5 Columns\'
)',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'Show Border',
                'name' => 'showBorder',
                'options' =>  [
                    'ng-model' => 'active.showBorder',
                    'ng-change' => 'save();relayout();',
                ],
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'type' => 'Text',
                'value' => '<hr/>',
            ],
        ];
    }
	
    /** @var integer $totalColumns */
    public $totalColumns = 2;
	
    /** @var string $showBorder */
    public $showBorder = 'No';
	
    /** @var array $parseField */
    public $parseField = [
        'column1' => 'renderColumn1',
        'column2' => 'renderColumn2',
        'column3' => 'renderColumn3',
        'column4' => 'renderColumn4',
        'column5' => 'renderColumn5',
    ];
	
    /** @var array $column1 */
    public $column1 = ['<column-placeholder></column-placeholder>'];
	
    /** @var array $column2 */
    public $column2 = ['<column-placeholder></column-placeholder>'];
	
    /** @var array $column3 */
    public $column3 = ['<column-placeholder></column-placeholder>'];
	
    /** @var array $column4 */
    public $column4 = ['<column-placeholder></column-placeholder>'];
	
    /** @var array $column5 */
    public $column5 = ['<column-placeholder></column-placeholder>'];
	
    /** @var string $renderColumn1 */
    public $renderColumn1 = "";
	
    /** @var string $renderColumn2 */
    public $renderColumn2 = "";
	
    /** @var string $renderColumn3 */
    public $renderColumn3 = "";
	
    /** @var string $renderColumn4 */
    public $renderColumn4 = "";
	
    /** @var string $renderColumn5 */
    public $renderColumn5 = "";
	
    /** @var string $toolbarName */
    public static $toolbarName = "Columns";
	
    /** @var string $category */
    public static $category = "Layout";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-columns";
	
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

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field ColumnField dari hasil render 
     */
    public function render() {
        return $this->renderInternal('template_render.php');
    }

}