<?php

class Asset extends CComponent {

    public static function publish($path) {
        if (strpos($path, Yii::getPathOfAlias('webroot')) == 0) {
            $path = Yii::app()->baseUrl . '/' . substr($path, strlen(Yii::getPathOfAlias('webroot')));
        }
        $result = str_replace("\\", "/", $path);
        return $result;
    }
    
}
