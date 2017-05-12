<?php

class FormGenerator extends CComponent
{

    public $form = '';


    public function steps()
    {
        return [];

    }//end steps()


    public function generate()
    {

    }//end generate()


    public static function listTemplates()
    {
        $dir  = Yii::getPathOfAlias('application.components.codegen.templates');
        $glob = array_slice(scandir($dir), 2);
        $list = [];
        foreach ($glob as $k => $l) {
            $t         = [];
            $t['name'] = $l;
            $t['icon'] = Asset::publish($dir.'/'.$l.'/icon.png');
            $list[]    = $t;
        }

        return $list;

    }//end listTemplates()


}//end class
