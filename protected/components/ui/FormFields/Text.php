<?php

class Text extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property Text.
	 */
    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Text / HTML / Angular :',
                'name' => 'value',
                'labelWidth' => '3',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'fieldHeight' => '20',
                'options' => array(
                    'ng-model' => 'active.value',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array(
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextArea',
            ),
        );
    }

	/** @var string $value */
    public $value;
	
	/** @var string $toolbarName */
    public static $toolbarName = "Text / HTML";
	
	/** @var string $category */
    public static $category = "Layout";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-font";

	/**
	 * @return field Fungsi ini untuk me-render field dan atributnya.
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
