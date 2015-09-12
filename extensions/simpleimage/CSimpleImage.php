<?php

// Created by Khanh Nam
class CSimpleImage extends CApplicationComponent{
    public function init() {
        parent::init();
        $dir = dirname(__FILE__);
        $alias = md5($dir);
        Yii::setPathOfAlias($alias,$dir);
        Yii::import($alias.'.simple_image');
    }
    public function load($filename){
        return new simple_image($filename);
    }
}
?>
