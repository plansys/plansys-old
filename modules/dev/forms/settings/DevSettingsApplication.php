<?php

class DevSettingsApplication extends Form {


    public function getFields() {
        return array (
            array (
                'name' => 'modalDialog1',
                'type' => 'Modal',
            ),
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
                        'file' => 'application.modules.dev.menus.Settings',
                        'title' => 'Settings',
                        'menuOptions' => array (
                            'ng-click' => 'location.href = Yii.app.createUrl(item.url);',
                        ),
                    ),
                    'col2' => array (
                        'type' => 'mainform',
                        'name' => 'col2',
                        'sizetype' => '%',
                    ),
                ),
            ),
            'inlineJS' => 'yang',
        );
    }

}