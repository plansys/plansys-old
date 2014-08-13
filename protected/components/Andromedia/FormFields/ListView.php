<?php

class ListView extends FormField {

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

    public $selected = '';
    public $list = '';
    public $listExpr = '';
    public $selectable = 'No';
    public $draggable = 'No';
    public $layoutPath = '';
    public $layout = '';
    public $layoutType = 'File';
    public $options = array();
    public $header = '';
    public $headerType = 'Text';
    public $headerPath = '';
    public $footer = '';
    public $footerType = 'Text';
    public $footerPath = '';
    public static $toolbarName = "ListView";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-reorder";
    protected $html = '';

    public function includeJS() {
        return array('list-view.js');
    }

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

    public function getFieldColClass() {
        return "col-sm-12";
    }

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

    public function render() {
        $this->addClass('form-group form-group-sm');
        $this->processExpr();

        return $this->renderInternal('template_render.php');
    }

}
