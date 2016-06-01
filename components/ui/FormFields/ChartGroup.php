<?php
/**
 * Class ColumnField
 * @author rizky
 */
class ChartGroup extends FormField {
    /**
     * @return array me-return array property ColumnField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Group Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Chart Title',
                'name' => 'title',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'yAxis Type',
                'name' => 'yAxisType',
                'options' => array (
                    'ng-model' => 'active.yAxisType',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'Single\', \'Double\')',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Group of Pie Charts',
                'name' => 'isPieGroup',
                'options' => array (
                    'ng-model' => 'active.isPieGroup',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'False\', \'True\')',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Margin',
                'name' => 'textField3',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'postfix' => 'px',
                'options' => array (
                    'ng-show' => 'active.isPieGroup == \'True\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'allowExtractKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
            array (
                'value' => '<hr/>',
                'type' => 'Text',
            ),
        );
    }
	
    /** @var integer $totalColumns */
    public $totalColumns = 1;
	
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
	
	/** @var string $name */
	public $name;
	
	/** @var string $title */
	public $title;
	
	/** @var string $options */
	public $options;
	
	/** @var string $yAxisType */
	public $yAxisType = "Single";
	
	/** @var bool $isPieGroup */
	public $isPieGroup = "False";
	
    /** @var array $column1 */
    public $column1 = ['<column-placeholder class="hide"></column-placeholder>'];
	public $column2 = ['<column-placeholder></column-placeholder>'];
	public $column3 = ['<column-placeholder></column-placeholder>'];
	public $column4 = ['<column-placeholder></column-placeholder>'];
	public $column5 = ['<column-placeholder></column-placeholder>'];
	
    /** @var string $renderColumn1 */
    public $renderColumn1 = "";
	public $renderColumn2 = "";
	public $renderColumn3 = "";
	public $renderColumn4 = "";
	public $renderColumn5 = "";
		
    /** @var string $toolbarName */
    public static $toolbarName = "Chart Group";
	
    /** @var string $category */
    public static $category = "Charts";
	
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
        if (trim($html == "<column-placeholder class='hide'></column-placeholder>")) {
            $html = "&nbsp;";
        }

        return $html;
    }
	
	public function includeJS() {
        return ['chart-group.js'];
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