<?php

class UploadFileNative extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'listExpr' => 'FormsController::$modelFieldList',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'KeyValueGrid',
                'name' => 'options',
                'label' => 'Options',
                'show' => 'Show',
                'allowExtractKey' => 'Yes',
            ),
        );
    }

    public $name;
    public $options = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Upload Native";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-upload";

    public function includeJS() {
        return ['upload-file-native.js'];
    }

    public function render() {
        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);
        $this->options['type'] = "file";
        $this->options['ps-name'] = $this->name;
        return $this->renderInternal('template_render.php');
    }

}

?>