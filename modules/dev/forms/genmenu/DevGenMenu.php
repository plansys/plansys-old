<?php
                            
class DevGenMenu extends Form {

    public function getForm() {
        return array (
            'title' => 'Gen Menu',
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
        );
    }

    public function getFields() {
        return array (
        );
    }

}