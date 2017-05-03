<?php

class DevSettingService extends Form {

    public function getForm() {
        return array (
            'title' => 'Settings Service',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
        );
    }

}