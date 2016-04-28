<?php

class DevGenModelRelForm extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Gen Model Rel ',
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
                'label' => 'Relation Name',
                'name' => 'name',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Type',
                'name' => 'type',
                'list' => array (
                    'ManyManyRelation' => 'Many Many',
                    'CHasManyRelation' => 'Has Many',
                    'CHasOneRelation' => 'Has One',
                    'CBelongsToRelation' => 'Belongs To',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Model',
                'name' => 'modelClass',
                'options' => array (
                    'ps-list' => 'params.models',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
            ),
            array (
                'label' => 'Foreign Key',
                'name' => 'fk',
                'options' => array (
                    'ps-list' => 'params.models',
                ),
                'type' => 'TextField',
            ),
        );
    }

}