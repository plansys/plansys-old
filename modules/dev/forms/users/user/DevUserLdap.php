<?php

class DevUserLdap extends Form {
    
    public function getForm() {
        return array (
            'title' => 'Import From LDAP / Active Directory',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'js/ldap.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Search User',
                        'name' => 'textField1',
                        'options' => array (
                            'ng-change' => 'search(q)',
                            'ng-model' => 'q',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'value' => '<div class=\"info\" ng-if=\"!isNewRecord\"><i class=\"fa fa-info-circle fa-nm fa-fw\"></i>&nbsp; 
Search menggunakan kotak disamping, gunakan * (bintang) sebagai Wildcard<Br/> &nbsp; lalu klik nama user yang ingin di-import:
</div>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'name' => 'dataSource1',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'username',
                        'label' => 'User Name',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'Display Name',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'cn',
                        'label' => 'Common Name',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'ou',
                        'label' => 'Org. Unit',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'dc',
                        'label' => 'Domain',
                        'options' => array (),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array (
                    'afterSelectionChange' => 'url:/dev/user/new?u={username}&f={fullname}&ldap=1',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

}