<?php

class DevDevGenModelImporter extends Form {

    public function getForm() {
        return array (
            'title' => 'Dev Gen Model Importer',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'TabImporter.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div class=\"alert alert-info\">
    INI IMPORTER
</div>',
            ),
        );
    }

}