<?php

class DevCrudMainForm extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Crud Main ',
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