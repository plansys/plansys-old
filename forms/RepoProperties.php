<?php
class RepoProperties extends Form{
    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div style=\"border-top:1px solid #ccc;text-align:center;padding-top:30px;color:#888;\">
<div ng-if=\"selected\">
    <div style=\"{{selected.type == \'dir\' ? \'margin:30px 0px -30px 0px\' : \'\' }}\" ng-bind-html=\"thumb\"></div>
    <h3>{{selected.name}}</h3>
    <h5 ng-if=\"selected.type != \'dir\' && selected.type != \'loading\' \">Size: {{selected.size | fileSize }}</h5>
    
    <a href=\" {{ getDownloadUrl(selected)}}\" style=\"margin-top:10px\" ng-if=\"selected.type != \'loading\'\" class=\"btn btn-success\">
    <i class=\"fa fa-download\"></i>
        Download
    </a>
    
    <a ng-click=\"onRemove(selected,dirs)\" style=\"margin-top:10px\" ng-if=\"selected.type != \'loading\'\" class=\"btn btn-danger\">
    <i class=\"fa fa-trash\"></i>
        Delete
    </a>
    
</div>
</div>',
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