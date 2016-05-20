<?php

/**
 * Class Text
 * @author rizky
 */
class Text extends FormField {

    public function actionCodePopUp() {
        
        Asset::registerJS('application.static.js.lib.ace');
        Yii::app()->controller->renderForm('TextPopUp',null,[],[
            'layout'=>'//layouts/blank'
        ]);
    }

    /**
     * @return array me-return array property Text.
     */
    public function getFieldProperties() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<a href=\"#\" 
    ng-click=\"popup.open()\"
    style=\'width:10%;padding:5px\' 
    class=\'pull-right\'>
    <div class=\"btn btn-xs btn-info\" 
    style=\"width:100%\">
        <i class=\"fa fa-expand\"></i>
    </div>
</a>
<div style=\'width:20%;\' class=\'pull-right\'>',
            ),
            array (
                'name' => 'display',
                'options' => array (
                    'ng-model' => 'active.display',
                    'ng-change' => 'save()',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => '[\'block\',\'inline\']',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'popup',
                'options' => array (
                    'width' => '1000',
                    'height' => '500',
                ),
                'mode' => 'url',
                'subForm' => 'application.components.ui.FormFields.TextPopUp',
                'url' => '/formfield/Text.codePopUp',
                'parentForm' => 'application.components.ui.FormFields.Text',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\'width:70%;\' class=\'pull-left\'>',
            ),
            array (
                'label' => 'Render In Editor',
                'name' => 'renderInEditor',
                'options' => array (
                    'ng-model' => 'active.renderInEditor',
                    'ng-change' => 'save()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\'Yes\',\'No\')',
                'labelWidth' => '7',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div class=\"clearfix\"></div>
<div class=\"text-editor-builder\">
  <div class=\"text-editor\" ui-ace=\"aceConfig({
  mode: \'html\',
  onLoad: aceLoaded
})\" 
ng-change=\"save()\" ng-delay=\"500\"
style=\"width:100%;height:300px;margin-bottom:-250px;position: relative !important;\" ng-model=\"active.value\">
    </div>
</div>
',
            ),
        );
    }

    public $renderInEditor = 'No';
    public $display        = 'block';
    public $type           = 'Text';

    /** @var string $value */
    public $value;

    /** @var string $toolbarName */
    public static $toolbarName = "Text / HTML";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-font";

    public function includeEditorJS() {
        return ['application.static.js.lib.ace'];
    }

    /**
     * @return string me-return string buffer contents
     */
    public function render() {
        $attributes = [
            'field' => $this->attributes,
            'form' => $this->formProperties,
        ];

        ob_start();
        if (strpos($this->value, "<?") !== false) {
            $model      = $this->model;
            $controller = Yii::app()->controller;

            $attrs = $this->renderParams;
            extract($attrs);

            eval('?>' . $this->value);
        } else {
            echo $this->value;
        }
        return ob_get_clean();
    }

}