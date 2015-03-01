<?php

class Asset extends CComponent {

    public static function publish($path, $extension = "", $isAlias = false) {
        if ($isAlias) {
            $path = Yii::getPathOfAlias($path) . $extension;
        }

        if (strpos($path, Yii::getPathOfAlias('webroot')) == 0) {
            $path = Yii::app()->baseUrl . substr($path, strlen(Yii::getPathOfAlias('webroot')));
        }
        $result = str_replace("\\", "/", $path);
        return $result;
    }

}
