<?php

/**
 * Class DataFilter
 * @author rizky
 */
class DataFilter extends FormField {
    
    /** @var string $name */
    public $name;
    
    /** @var string $datasource */
    public $datasource;
    
    /** @var string $params */
    public $params;
    
    /**
     * @return array me-return array property DataFilter.
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
    
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('data-filter.js');
    }

    

}
