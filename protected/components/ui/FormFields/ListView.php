<?php

class ListView extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property ListView.
	 */
    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Header',
                'name' => 'headerType',
                'options' => array(
                    'ng-model' => 'active.headerType',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'Text' => 'Text',
                    'File' => 'File',
                ),
                'listExpr' => 'array(\\"Text\\",\\"File\\")',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'File Path (.php)',
                'name' => 'headerPath',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-show' => 'active.headerType == \'File\'',
                    'ng-model' => 'active.headerPath',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Header HTML',
                'fieldname' => 'header',
                'validAction' => 'save();',
                'language' => 'html',
                'options' => array(
                    'ng-show' => 'active.headerType != \'File\'',
                ),
                'type' => 'ExpressionField',
            ),
            '<hr/>',
            array(
                'label' => 'Layout',
                'name' => 'layoutType',
                'options' => array(
                    'ng-model' => 'active.layoutType',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'Text' => 'Text',
                    'File' => 'File',
                ),
                'listExpr' => 'array(\\"Text\\",\\"File\\")',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'File Path (.php)',
                'name' => 'layoutPath',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-show' => 'active.layoutType == \'File\'',
                    'ng-model' => 'active.layoutPath',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Layout',
                'fieldname' => 'layout',
                'validAction' => 'save();',
                'language' => 'html',
                'options' => array(
                    'ng-show' => 'active.layoutType != \'File\'',
                ),
                'type' => 'ExpressionField',
            ),
            '<hr/>',
            array(
                'label' => 'Footer',
                'name' => 'footerType',
                'options' => array(
                    'ng-model' => 'active.footerType',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'Text' => 'Text',
                    'File' => 'File',
                ),
                'listExpr' => 'array(\\"Text\\",\\"File\\")',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'File Path (.php):',
                'name' => 'footerPath',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-show' => 'active.footerType == \'File\'',
                    'ng-model' => 'active.footerPath',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Footer HTML',
                'fieldname' => 'footer',
                'validAction' => 'save();',
                'language' => 'html',
                'options' => array(
                    'ng-show' => 'active.footerType != \'File\'',
                ),
                'type' => 'ExpressionField',
            ),
            '<hr/>',
            array(
                'label' => 'Draggable',
                'name' => 'draggable',
                'options' => array(
                    'ng-model' => 'active.draggable',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
                'listExpr' => 'array(\\"Yes\\",\\"No\\")',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Selectable',
                'name' => 'selectable',
                'options' => array(
                    'ng-model' => 'active.selectable',
                    'ng-change' => 'save()',
                ),
                'list' => array(
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
                'listExpr' => 'array(\\"Yes\\",\\"No\\")',
                'labelWidth' => '5',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            '<hr/>',
            array(
                'label' => 'Data Source Expression',
                'fieldname' => 'listExpr',
                'validAction' => 'active.list = result;save();',
                'options' => array(
                    'ng-hide' => 'active.options[\'ng-form-list\'] != null',
                ),
                'type' => 'ExpressionField',
            ),
            array(
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

	/** @var string variable untuk menampung selected */
    public $selected = '';
	
	/** @var string variable untuk menampung list */
    public $list = '';
	
	/** @var string variable untuk menampung list expression */
    public $listExpr = '';
	
	/** @var string variable untuk menampung kondisi selectable field dengan default No */
    public $selectable = 'No';
	
	/** @var string variable untuk menampung kondisi draggable field dengan default No */
    public $draggable = 'No';
	
	/** @var string variable untuk menampung layoutPath */
    public $layoutPath = '';
	
	/** @var string variable untuk menampung layout */
    public $layout = '';
	
	/** @var string variable untuk menampung tipe layout dengan default File*/
    public $layoutType = 'File';
	
	/** @var array variable untuk menampung array options */
    public $options = array();
	
	/** @var string variable untuk menampung isi header */
    public $header = '';
	
	/** @var string variable untuk menampung tipe header dengan deafult Text */
    public $headerType = 'Text';
	
	/** @var string variable untuk menampung path header */
    public $headerPath = '';
	
	/** @var string variable untuk menampung footer */
    public $footer = '';
	
	/** @var string variable untuk menampung tipe footer dengan default Text */
    public $footerType = 'Text';
	
	/** @var string variable untuk menampung path footer */
    public $footerPath = '';
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "ListView";
	
	/** @var string variable untuk menampung category */
    public static $category = "User Interface";
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-reorder";
	
	/** 
	 * @var string variable untuk menampung html.
	 * @access protected	
	*/
    protected $html = '';
	
	/**
	 * @return array Fungsi ini akan me-return array javascript yang di-include. Defaultnya akan meng-include.
	*/
    public function includeJS() {
        return array('list-view.js');
    }

	/**
	 * @return array Fungsi ini akan memproses expression menjadi array lalu mereturn array tersebut.
	*/
    public function processExpr() {
        if ($this->listExpr != "") {
            ## evaluate expression
            $this->list = $this->evaluate($this->listExpr, true);
        }

        $this->loadLayout('layout');
        $this->loadLayout('header');
        $this->loadLayout('footer');

        return array(
            'list' => $this->list,
            'layout' => $this->layout
        );
    }

	/**
	 * @return integer Fungsi ini akan me-return string untuk menentukan width fields.
	 */
    public function getFieldColClass() {
        return "col-sm-12";
    }

	/**
	 * @param string $layout Parameter untuk melempar layout field.
	 * @return null Fungsi ini akan me-load layout dan akan menjalankan beberapa function didalamnya jika type layout adalah 'File'.
	 */
    private function loadLayout($layout) {
        $type = $layout . "Type";
        $path = $layout . "Path";
        if ($this->$type == 'File') {
            if (strtolower(substr($this->$path, -4)) == ".php") {
                $this->$path = substr($this->$path, 0, -4);
            }

            if (strpos($this->$path, ".") === false) {
                $reflector = new ReflectionClass($this->model);
                $filepath = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . $this->$path . ".php";
            } else {
                $filepath = Yii::getPathOfAlias($this->$path) . ".php";
            }
            if (is_file($filepath)) {
                $this->$layout = Yii::app()->controller->renderFile($filepath,array(
                    'list' => $this,
                    'builder' => $this->builder
                ),true);
            }
        }
    }

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
	 */
    public function render() {
        $this->addClass('form-group form-group-sm');
        $this->processExpr();

        return $this->renderInternal('template_render.php');
    }

}
