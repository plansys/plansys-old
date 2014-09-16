<?php
/**
 * Class SectionHeader
 * @author rizky
 */
class SectionHeader extends FormField {
    /**
     * @return array me-return array property SectionHeader.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Title',
                'name' => 'title',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
        );
    }

    /** @var string $title */
    public $title = "Section";
	
    /** @var string $toolbarName */
    public static $toolbarName = "Section Header";
	
    /** @var string $category */
    public static $category = "Layout";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-archive";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array(
            'section-header.js'
        );
    }

}
