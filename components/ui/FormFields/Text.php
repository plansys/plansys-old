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
                'label' => 'Text / HTML / Angular :',
                'name' => 'value',
                'labelWidth' => '3',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'fieldHeight' => '20',
                'options' => array (
                    'ng-model' => 'active.value',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextArea',
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
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
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
