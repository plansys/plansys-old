<?php

/**
 * Class Text
 * @author rizky
 */
class Text extends FormField {

    /**
     * @return array me-return array property Text.
     */
    public function getFieldProperties() {
        return array (
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
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'value' => '<div style=\"margin:0px -5px;\">
    <div id=\"text-editor\" ui-ace=\"{
  useWrapMode : true,
  showGutter: false,
  theme:\'monokai\',
  mode: \'html\',
  onLoad: aceLoaded,
}\" 
ng-change=\"save()\" ng-delay=\"500\"
style=\"width:100%;height:300px;margin-bottom:-250px;font-size:13px;position: relative !important;\" ng-model=\"active.value\">
    
    </div>
</div>',
                'type' => 'Text',
            ),
        );
    }

    public $renderInEditor = 'No';

    /** @var string $value */
    public $value;

    /** @var string $toolbarName */
    public static $toolbarName = "Text / HTML";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-font";

    public function includeJS() {
        return array('js');
    }

    /**
     * @return string me-return string buffer contents
     */
    public function render() {
        $attributes = array(
            'field' => $this->attributes,
            'form' => $this->formProperties,
        );

        ob_start();
        if (strpos($this->value, "<?") !== false) {
            eval("?>$this->value");
        } else {
            echo $this->value;
        }
        return ob_get_clean();
    }

}