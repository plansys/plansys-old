<?php

class DevGenModelImporter extends Form {

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
                'value' => '<style>
    .importer-head {
        border-bottom:1px solid #ddd;
        background:#fafafa;
        padding:0px;
        margin:0px -15px;
    }
</style>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"margin-right:-30px;\\">',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Import Source File',
                        'name' => 'importFile',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'options' => array (
                    'class' => 'importer-head',
                ),
                'perColumnOptions' => array (
                    'style' => 'padding:10px 2px 2px 2px',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}