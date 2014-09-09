<?php

class DevUserRoleList extends Form {
    public function getFields() {
        return array (
        );
    }
    public function getForm() {
        return array (
            'title' => 'UserRoleList',
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
    
}