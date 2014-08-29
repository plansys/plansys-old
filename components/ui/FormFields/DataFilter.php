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
    
    /** @var string $filters */
    public $filters;
    
    public $options = array(
        'ps-ds-sql' => 'DataFilter::generateSQL($fb)'
    );
    
    /** @var string $toolbarName */
    public static $toolbarName = "Data Filter";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-filter";
    
    public $filterOperators = array(
        'string' => array(
            'Contains',
            'Does Not Contain',
            'Is Equal To',
            'Starts With',
            'Ends With',
            'Is Any Of',
            'Is Not Any Of',
            'Is Empty'
        )
    );
    
    /**
     * @return array me-return array property DataFilter.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Data Filter Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Data Source Name',
                'name' => 'datasource',
                'options' => array (
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Generate Filters',
                'buttonType' => 'success',
                'icon' => 'magic',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \'\'',
                    'ng-click' => 'generateFilters()',
                ),
                'type' => 'LinkButton',
            ),
            '<div class="clearfix"></div>',
            array (
                'title' => 'Filters',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'filters',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataFilterListForm',
                'fieldWidth' => '12',
                'options' => array (
                    'style' => 'margin-top:-13px;padding-left:7px;',
                    'ng-model' => 'active.filters',
                    'ng-change' => 'save()',
                ),
                'type' => 'ListView',
            ),
        );
    }
    
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('data-filter.js');
    }

    
    public static function generateSQL($fb) {
        var_dump($fb);
    }
    

}
