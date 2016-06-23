<?php

class HelpWelcome extends Form {

    public function getForm() {
        return array (
            'title' => 'Welcome',
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
            array (
                'type' => 'PopupWindow',
                'name' => 'popupWindow0',
                'mode' => 'url',
                'parentForm' => 'application.modules.help.forms.tutorial.HelpWelcome',
            ),
        );
    }

}