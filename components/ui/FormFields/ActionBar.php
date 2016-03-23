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
                'listExpr' => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show Option Menu',
                'name' => 'showOptionsBar',
                'options' => array (
                    'ng-model' => 'active.showOptionsBar',
                    'ng-change' => 'save()',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'listExpr' => '[\'No\', \'Yes\']',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'First Tab',
                'name' => 'firstTabName',
                'options' => array (
                    'ng-model' => 'active.firstTabName',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-if' => 'active.showSectionTab == \'Yes\'',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'TextField',
            ),
        );
    }

    public function getDashboardMode() {
        return (Yii::app()->controller->module->id == "dev" &&
                Yii::app()->controller->id == "forms" &&
                Yii::app()->controller->action->id == "dashboard" ? "edit" : "view");
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        $return = [];
        if ($this->showOptionsBar == "Yes") {
            $return[] = 'html2canvas.min.js';
        }
        $return[] = 'action-bar.js';
        return $return;
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
    public $showOptionsBar = "No";

    public function getPortlets() {
        $portlets = $this->builder->findAllField(['type' => 'Portlet']);
        $results = [];
        foreach ($portlets as $p) {
            $results[] = [
                'name' => $p['name'],
                'title' => $p['title'],
            ];
        }

        return $results;
    }

}