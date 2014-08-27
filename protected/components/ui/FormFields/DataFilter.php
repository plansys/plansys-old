<?php

/**
 * Class DataFilter
 * @author rizky
 */
class DataFilter extends FormField {
    public $name;
    public $datasource;
    public $params;
    
    /**
     * @return array Fungsi ini akan me-return array property HiddenField.
     */
    public function getFieldProperties() {
        return array(
            array(
                'label' => 'Data Source Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'Parameters',
                'fieldname' => 'params',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }
    
    public function includeJS() {
        return array('data-filter.js');
    }

    

}
