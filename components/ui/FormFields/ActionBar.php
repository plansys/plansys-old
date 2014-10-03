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
                'label' => 'Title',
                'name' => 'title',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' => array (
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left',
                ),
                'fieldOptions' => array (
                    'auto-grow' => '',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Show Tab',
                'name' => 'showSectionTab',
                'options' => array (
                    'ng-model' => 'active.showSectionTab',
                    'ng-change' => 'save()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '3',
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