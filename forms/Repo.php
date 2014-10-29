<?php
class Repo extends Form {
	
    public function getForm() {
        return array (
            'title' => 'Repo',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'repo/repo.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Upload',
                        'group' => 'a',
                        'icon' => 'upload',
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'value' => '<div style=\"position:fixed;top:40px;left:20px;z-index:10;width:100%;\">
<div class=\"input-group\" style=\"margin:-5px 0px 0px -20px;width:53%;\">
  <span class=\"input-group-addon\" style=\"background:white;border:0px;\"><i class=\"fa fa-lg fa-paper-plane-o\"></i></span>
  <input type=\"text\" class=\"form-control\" ng-keydown=\"changeDir($event)\" ng-model=\"currentDir\" style=\"padding:4px 10px 3px 10px;border:1px solid #ccc;\">
</div>
    
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div style=\"width:55%;position:absolute;left:0px;top:0px;bottom:-600px;border-right:1px solid #ddd;height:100%;\">
<div style=\"margin:-34px -1px 0px 0px;-moz-user-select:none !important;-webkit-user-select:none !important;\">',
                'type' => 'Text',
            ),
            array (
                'name' => 'dataSource1',
                'fieldType' => 'php',
                'php' => 'RepoManager::model()->browse(RepoManager::getModuleDir())[\\\'item\\\'];',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'type',
                        'label' => '',
                        'options' => array (
                            'width' => '34',
                            'resizable' => 'false',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (
                            'loading' => '<i class=\\\'fa fa-nm fa-folder-open\\\'></i>',
                            'dir' => '<i class=\\\'fa fa-nm fa-folder\\\'></i>',
                            '*' => '<i class=\\\'fa fa-nm fa-file-o\\\'></i>',
                            'rx:/php|css|js|html/i' => '<i class=\\\'fa fa-nm fa-file-code-o\\\'></i>',
                            'rx:/png|jpg|tif|jpeg|psd|gif|exif|bmp|tga/i' => '<i class=\\\'fa fa-nm fa-file-image-o\\\'></i>',
                        ),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'File Name',
                        'options' => array (
                            'sortable' => 'false',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'size',
                        'label' => 'Size',
                        'options' => array (
                            'cellFilter' => 'fileSize',
                            'width' => '100',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array (),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array (
                    'fixedHeader' => 'always',
                    'enableColumnResize' => 'false',
                    'afterSelectionChange' => 'js: $scope.click',
                ),
                'type' => 'DataGrid',
            ),
            array (
                'value' => '</div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div style=\"width:45%;position:absolute;right:1px;top:0px;bottom:-600px;border-left:1px solid #ddd;z-index:1;\">
<div id=\"properties\" style=\"position:fixed;\">
',
                'type' => 'Text',
            ),
            array (
                'name' => 'repoProperties',
                'subForm' => 'application.forms.RepoProperties',
                'type' => 'SubForm',
            ),
            array (
                'value' => '</div> 
</div>',
                'type' => 'Text',
            ),
        );
    }

}