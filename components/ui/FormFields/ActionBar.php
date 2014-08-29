<?php
/**
 * Class ActionBar
 * @author rizky
 */
class ActionBar extends FormField {

    /**
     * @return array me-return array property ActionBar.
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
                'label' => 'Show Section Tab',
                'name' => 'showSectionTab',
                'options' => array (
                    'ng-model' => 'active.showSectionTab',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Title Breacrumb Link',
                'fieldname' => 'titleLink',
                'show' => 'Show',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return array('action-bar.js');
    }

    /** @var string $toolbarName */
    public static $toolbarName = "Action Bar";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-suitcase";

    
    /** @var array $parseField */
    public $parseField = array(
        'linkBar' => 'renderLinkBar',
    );
    
    /** @var array $linkBar */
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
    
    /** @var string $renderLinkBar */
    public $renderLinkBar = "";
    
    /** @var string $title */
    public $title = "{{form.title}}";
    
    /** @var array $titleLink */
    public $titleLink = array();
    
    /** @var string $bottomLeft */
    public $bottomLeft = "";
    
    /** @var string $bottomRight */
    public $bottomRight = "";
    
    public $showSectionTab = "Yes";

}
