<?php

/**
 * Class DataGrid
 * @author rizky
 */
class RepoBrowser extends FormField {

    public $name = '';
    public $showBrowseButton = 'Yes';
    public $options = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Repo Browser";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-square fa-nm";

    public function getFieldProperties() {
        return [
            [
                'label' => 'Repo Browser Name',
                'name' => 'name',
                'options' => [
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
            [
                'label' => 'Show Browse Button',
                'name' => 'showBrowseButton',
                'options' => [
                    'ng-model' => 'active.showBrowseButton',
                    'ng-change' => 'save();',
                ],
                'listExpr' => 'array(\'Yes\',\'No\')',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
            [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    public function includeJS() {
        return ['repo-browser.js', 'repo-dialog.js'];
    }
    
    public function actionBrowse() {
        $file = "repo-dialog.php";
        $reflector = new ReflectionClass($this);
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());
        
        include($path);
    }

}
