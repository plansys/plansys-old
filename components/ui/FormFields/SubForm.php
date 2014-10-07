<?php

/**
 * Class DataGrid
 * @author rizky
 */
class SubForm extends FormField {

    public $name = '';
    public $subForm = '';
    public $options = '';
    public $inlineJS = '';

    /** @var string $toolbarName */
    public static $toolbarName = "Sub Form";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-file-text-o fa-nm";

    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'SubForm',
                'name' => 'subForm',
                'options' => array(
                    'ng-model' => 'active.subForm',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'FormBuilder::listForm(null, true)',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Inline JS',
                'name' => 'inlineJS',
                'options' => array(
                    'ng-model' => 'active.inlineJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    private $_subformClass = "";

    public function getSubFormClass() {
        if ($this->_subformClass == "") {
            $this->_subformClass = array_pop(explode(".", $this->subForm));
        }
        return $this->_subformClass;
    }

    public function getCtrlName() {
        return $this->subFormClass . ucfirst($this->name);
    }

    public function getRenderUrl() {
        return Yii::app()->controller->createUrl('/FormField/SubForm.render', array(
                'name' => $this->name,
                'class' => $this->subForm,
                'js' => $this->inlineJS
        ));
    }

    public function actionRender($name, $class, $js) {
        $this->name = $name;
        $this->subForm = $class;
        $this->inlineJS = $js;
        
        
        ## render
        Yii::import($class);
        $fb = FormBuilder::load($this->subFormClass);

        $html = '<div ng-controller="' . $this->ctrlName . 'Controller">';
        $html .= $fb->render(null, array(
            'wrapForm' => false
        ));

        $jspath = explode(".", $this->subForm);
        array_pop($jspath);
        $jspath = implode(".", $jspath);

        $inlineJS = str_replace("/", DIRECTORY_SEPARATOR, trim($this->inlineJS, "/"));
        $inlineJS = Yii::getPathOfAlias($jspath) . DIRECTORY_SEPARATOR . $inlineJS;
        if (is_file($inlineJS)) {
            $inlineJS = file_get_contents($inlineJS);
        } else {
            $inlineJS = '';
        }
        
        $html .= '</div>';
        $controller = include("SubForm/controller.js.php");
        $html .= '<script>' . $controller . '</script>';
        echo $html;
    }

    public function render() {
        Yii::import($this->subForm);

        if ($this->subFormClass == get_class($this)) {
            return '<center><i class="fa fa-warning"></i> Error Rendering SubForm: Subform can not be the same as its parent</center>';
        } else {
            $attrs = is_array($this->options) ? $this->expandAttributes($this->options) : '';
            
            $html = '<div ' . $attrs . ' ng-include="\'' . $this->renderUrl . '\'"></div>';
            return $html;
        }
    }

}
