<?php
class RepoProperties extends Form{
    public function getFields() {
        return array (
            array (
                'name' => 'uploadFile',
                'label' => 'Upload File',
                'fieldWidth' => 8,
                'options' => array (
                    'ng-model' => 'active.path',
                ),
                'type' => 'UploadFile',
            ),
        );
    }


    public function getForm() {
        return array (
            'formTitle' => 'RepoProperties',
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
    public $uploadFile;
}