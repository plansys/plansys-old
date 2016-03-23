<?php

class SysServiceNotFound extends Form {

    public function getForm() {
        return array (
            'title' => 'Service Not Found',
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
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div class=\"panel panel-default\"
    style=\"width:500px;margin:40px auto;\">
    <div class=\"panel-body text-center\"
        style=\"padding:50px 20px;\">
        <i class=\"fa fa-warning fa-3x\">
        </i>
        <br/>
        <h4 style=\"margin:10px 0px 0px 0px;line-height:25px;\">
            Can\'t Start Service:<br/>
            {{params.msg}}
        </h4>
        
    </div>
</div>',
            ),
        );
    }

}