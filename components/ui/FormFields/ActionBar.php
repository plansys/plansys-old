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
        return  [
             [
                'label' => 'Title',
                'name' => 'title',
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'options' =>  [
                    'ng-model' => 'active.title',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left',
                ],
                'fieldOptions' =>  [
                    'auto-grow' => '',
                ],
                'type' => 'TextArea',
            ],
             [
                'label' => 'Show Tab',
                'name' => 'showSectionTab',
                'options' =>  [
                    'ng-model' => 'active.showSectionTab',
                    'ng-change' => 'save()',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '3',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'label' => 'First Tab',
                'name' => 'firstTabName',
                'labelWidth' => '3',
                'options' =>  [
                    'ng-model' => 'active.firstTabName',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-if' => 'active.showSectionTab == \\\'Yes\\\'',
                ],
                'labelOptions' =>  [
                    'style' => 'text-align:left;',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Title Breacrumb Link',
                'name' => 'titleLink',
                'show' => 'Show',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['action-bar.js'];
    }

    /** @var string $toolbarName */
    public static $toolbarName = "Action Bar";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-suitcase";

    
    /** @var array $parseField */
    public $parseField = [
        'linkBar' => 'renderLinkBar',
    ];
    
    /** @var array $linkBar */
    public $linkBar = [
        [
            'label' => 'Save',
            'buttonType' => 'success',
            'buttonSize' => 'btn-sm',
            'type' => 'LinkButton',
            'displayInline' => true,
            'options' => [
                'ng-click' => 'form.submit(this)'
            ]
        ],
    ];
    
    /** @var string $renderLinkBar */
    public $renderLinkBar = "";
    
    /** @var string $title */
    public $title = "{{form.title}}";
    
    public $firstTabName = 'General';
    
    /** @var array $titleLink */
    public $titleLink = [];
    
    /** @var string $bottomLeft */
    public $bottomLeft = "";
    
    /** @var string $bottomRight */
    public $bottomRight = "";
    
    public $showSectionTab = "Yes";

}