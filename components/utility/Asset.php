<?php

class Asset extends CComponent {


    public static function resolveAlias($path) {
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
        return $path;
    }

    public static function publish($path, $isAlias = false) {
        if ($isAlias) {
            $path = Asset::resolveAlias($path);
        }

        if (strpos($path, Yii::getPathOfAlias('webroot')) == 0) {
            $path = Yii::app()->baseUrl . substr($path, strlen(Yii::getPathOfAlias('webroot')));
        }
        $result = str_replace("\\", "/", $path);
        return $result;
    }

    public static function registerJS($includeJS) {
        if (is_string($includeJS)) {
            $includeJS = [$includeJS];
        }

        if (!empty($includeJS)) {
            foreach ($includeJS as $js) {
                $jspath = realpath(Asset::resolveAlias($js));
                if (is_dir($jspath)) {
                    $path = Asset::publish($jspath);
                    $files = glob($jspath . "/*");

                    foreach ($files as $p) {
                        if (pathinfo($p, PATHINFO_EXTENSION) != "js") {
                            continue;
                        }

                        $p = str_replace($jspath, '', realpath($p));

                        Yii::app()->clientScript->registerScriptFile($path . str_replace("\\", "/", $p), CClientScript::POS_END);
                    }
                } else if (is_file($jspath)) {
                    Yii::app()->clientScript->registerScriptFile(
                        Asset::publish($jspath), CClientScript::POS_END
                    );
                }
            }
        }
    }
}
