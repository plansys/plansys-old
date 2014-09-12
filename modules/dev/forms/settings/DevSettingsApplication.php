<?php

class DevSettingsApplication extends Form {


    public function getFields() {
        return array (
        );
    }

    public function getForm() {
        return array (
            'title' => 'SettingsApplication Kalau cuman ganti ganti gini aja sih ga masalah',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                    ),
                ),
            ),
            'inlineJS' => 'yang',
        );
    }

}