<?php

class Help extends CComponent {
    
    public static function get($menu, $type) {
        return file_get_contents(Yii::app()->controller->module->basePath .'/help/'. $menu .'/'. $type.'.html');
    }
    
}