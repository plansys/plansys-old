<?php

class ActionBar extends FormField {

    /**
     * @return array Fungsi ini akan me-return array property ActionBar.
     */
    public function getFieldProperties() {
        return array (
            array (
                'name' => 'title',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'prefix' => 'Title',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Title Link',
                'fieldname' => 'titleLink',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public function includeJS() {
        return array('action-bar.js');
    }

    /** @var string variable untuk menampung toolbarName */
    public static $toolbarName = "Action Bar";

    /** @var string variable untuk menampung category */
    public static $category = "Layout";

    /** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon = "fa fa-suitcase";

    /** @var array variable untuk menampung parseField */
    public $parseField = array(
        'linkBar' => 'renderLinkBar',
    );
    public $linkBar = array(
        array(
            'label' => 'Cancel',
            'buttonType' => 'default',
            'buttonSize' => 'btn-sm',
            'type' => 'LinkButton',
            'displayInline' => true,
            'options' => array(
                'ng-show' => 'form.canGoBack()',
                'ng-click' => 'form.goBack()'
            )
        ),
        array(
            'label' => 'Save',
            'buttonType' => 'success',
            'buttonSize' => 'btn-sm',
            'type' => 'LinkButton',
            'displayInline' => true,
            'options' => array(
                'ng-click' => 'form.submit(this)'
            )
        ),
    );
    public $renderLinkBar = "";
    public $title = "{{form.title}}";
    public $titleLink = array();
    public $bottomLeft = "";
    public $bottomRight = "";

}
