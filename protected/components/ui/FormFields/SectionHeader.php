<?php

class SectionHeader extends FormField {

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

    public $title = "Section";
    public static $toolbarName = "Section Header";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-archive";


}
