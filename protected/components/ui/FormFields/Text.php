<?php

class Text extends FormField {

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

    public $value;
    public static $toolbarName = "Text / HTML";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-font";

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
