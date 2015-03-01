<?php

class Asset extends CComponent {

    public static function publish($path, $isAlias = false) {
        if ($isAlias) {
            $pathArr = explode(".", $path);
            if (in_array($pathArr[count($pathArr) - 1], ["php", "js", "css"])) {
                $ext = array_pop($pathArr);

                $pathNew = Yii::getPathOfAlias(implode(".", $pathArr)) . "." . $ext;
                if (is_file($pathNew)) {
                    $path = $pathNew;
                } else {
                    $path = Yii::getPathOfAlias($path);
                }
            } else {
                $path = Yii::getPathOfAlias($path);
            }
        }

        if (strpos($path, Yii::getPathOfAlias('webroot')) == 0) {
            $path = Yii::app()->baseUrl . substr($path, strlen(Yii::getPathOfAlias('webroot')));
        }
        $result = str_replace("\\", "/", $path);
        return $result;
    }

}
