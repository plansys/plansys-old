<?php
Yii::import("application.modules.nfy.models.NfyDbMessage");
class NfyMessagesForm extends NfyDbMessage {
    
    public function getForm() {
        return array (
            'title' => 'Db Message',
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
                'linkBar' => array (
                    array (
                        'label' => 'Kembali',
                        'options' => array (
                            'href' => 'url:/widget/NfyWidget.history',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => 'Notification Detail',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Reserved On',
                        'name' => 'reserved_on',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Deleted On',
                        'name' => 'deleted_on',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Read On',
                        'name' => 'read_on',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Sent On',
                        'name' => 'sent_on',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Mimetype',
                        'name' => 'mimetype',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Queue',
                        'name' => 'queue_id',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Created On',
                        'name' => 'created_on',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Sender',
                        'name' => 'sender_id',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Message',
                        'name' => 'message_id',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Subscription',
                        'name' => 'subscription_id',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Status',
                        'name' => 'status',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Timeout',
                        'name' => 'timeout',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Body',
                        'name' => 'body',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Identifier',
                        'name' => 'identifier',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
        );
    }

}