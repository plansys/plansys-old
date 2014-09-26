<?php

class MigrationController extends Controller {

    public function actionIndex() {
        $dir = Yii::getPathOfAlias('app.migrations');

        if (!is_dir($dir)) {
            mkdir($dir);
            mkdir($dir . DIRECTORY_SEPARATOR . '1');
        }

        $mig_dir = glob($dir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

        $migrations = array();
        foreach ($mig_dir as $d) {
            $sqls = glob($d . DIRECTORY_SEPARATOR . "*.sql");
            $idx = str_replace($dir . DIRECTORY_SEPARATOR, '', $d);
            if (!isset($migrations[$idx])) {
                $migrations = array($idx => array()) + $migrations;
            }

            foreach ($sqls as $sql_file) {
                $data = file_get_contents($sql_file);
                $file = str_replace($mig_dir . DIRECTORY_SEPARATOR . $idx . DIRECTORY_SEPARATOR, '', $sql_file);
                $migrations[$idx][$file] = $data;
            }
        }
        var_dump($migrations);
        die();
    }

}
