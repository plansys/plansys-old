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
                'label' => 'Section Header Title (HTML Allowed):',
                'name' => 'title',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'options' => array (
                    'ng-model' => 'active.title',
                ),
                'fieldOptions' => array (
                    'auto-grow' => 'true',
                ),
                'type' => 'TextArea',
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
        return [
            'section-header.js'
        ];
    }

}