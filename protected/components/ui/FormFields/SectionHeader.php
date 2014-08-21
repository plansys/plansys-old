<?php

class SectionHeader extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property SectionHeader.
	 */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Title',
                'name' => 'title',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
        );
    }

	/** @var string variable untuk menampung title field SectionHeader */
    public $title = "Section";
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "Section Header";
	
	/** @var string variable untuk menampung category */
    public static $category = "Layout";
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-archive";


}
