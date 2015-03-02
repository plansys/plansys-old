<?php

class DevSettingsEmailSes extends Form{
    public $emailAccessKeyId;
    public $emailSecretAccessKey;
    public $emailSessionToken;
    public $emailRegion;
    public $emailRateLimit;
    
    public function getForm() {
        return array (
            'title' => 'Settings Email Ses',
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
                'label' => 'Access Key ID',
                'name' => 'emailAccessKeyId',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Secret Access Key',
                'name' => 'emailSecretAccessKey',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Region',
                'name' => 'emailRegion',
                'list' => array (
                    'us-east-1' => 'us-east-1',
                    'us-west-2' => 'us-west-2',
                    'eu-west-1' => 'eu-west-1',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Rate Limit',
                'name' => 'emailRateLimit',
                'type' => 'TextField',
            ),
        );
    }
}
