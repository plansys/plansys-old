<?php
                            
class DevMantab extends User {

    public function getForm() {
        return array (
            'title' => 'Mantab',
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
                            'href' => 'url:/dev/mantab/index',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Simpan',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"!isNewRecord\\" class=\\"separator\\"></div>',
                        'renderInEditor' => 'Yes',
                    ),
                    array (
                        'label' => 'Hapus',
                        'buttonType' => 'danger',
                        'options' => array (
                            'ng-if' => '!isNewRecord',
                            'href' => 'url:/dev/mantab/delete?id={model.id}',
                            'confirm' => 'Apakah Anda Yakin ?',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '{{ isNewRecord ? \\\'Tambah Mantab\\\' : \\\'Update Mantab\\\'}}',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'type' => 'ColumnField',
                'column1' => array (
                    array (
                        'name' => 'is_deleted',
                        'type' => 'TextField',
                        'label' => 'Is Deleted',
                    ),
                    array (
                        'name' => 'nip',
                        'type' => 'TextField',
                        'label' => 'Nip',
                    ),
                    array (
                        'name' => 'fullname',
                        'type' => 'TextField',
                        'label' => 'Fullname',
                    ),
                    array (
                        'name' => 'email',
                        'type' => 'TextField',
                        'label' => 'Email',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'name' => 'phone',
                        'type' => 'TextField',
                        'label' => 'Phone',
                    ),
                    array (
                        'name' => 'username',
                        'type' => 'TextField',
                        'label' => 'Username',
                    ),
                    array (
                        'name' => 'password',
                        'type' => 'TextField',
                        'label' => 'Password',
                    ),
                    array (
                        'name' => 'last_login',
                        'type' => 'DateTimePicker',
                        'label' => 'Last Login',
                        'fieldType' => 'datetime',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
            ),
        );
    }

}