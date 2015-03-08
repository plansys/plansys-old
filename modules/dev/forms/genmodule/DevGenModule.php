<?php

class DevGenModule extends Form {

    public $name;
    public $path;
    public $classPath;
    public $controllers;

    public function load($module) {
        $m = explode(".", $module);
        if (count($m) == 2) {
            $name      = $m[1];
            $class     = ucfirst($m[1]) . "Module";
            $basePath  = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $path      = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";
            if (is_dir($path) && is_file($classPath)) {
                $this->name      = $name;
                $this->path      = $path;
                $this->classPath = $classPath;
            }
        }
    }

    public function getForm() {
        return array(
            'title'  => 'Generate Module',
            'layout' => array(
                'name' => '2-cols',
                'data' => array(
                    'col1' => array(
                        'size'     => '200',
                        'sizetype' => 'px',
                        'type'     => 'menu',
                        'name'     => 'col1',
                        'file'     => 'application.modules.dev.menus.GenModule',
                        'title'    => 'test',
                        'icon'     => 'fa-empire',
                    ),
                    'col2' => array(
                        'type'     => 'mainform',
                        'name'     => 'col2',
                        'sizetype' => '%',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<!-- MODULE INFO TAB -->
<tabset class=\'tab-set\'>
<tab heading=\"Module Info\">',
                'type' => 'Text',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Module Name',
                        'name' => 'name',
                        'type' => 'LabelField',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Class Path',
                        'name' => 'classPath',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module Directory',
                        'name' => 'path',
                        'type' => 'LabelField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Oke deh checking test',
                'type' => 'SectionHeader',
            ),
            array (
                'value' => '<!-- ACCESS CONTROL TAB -->
</tab><tab heading=\"Access Control\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<!-- TAB CLOSER -->
</tab></tabset>',
                'type' => 'Text',
            ),
        );
    }

}