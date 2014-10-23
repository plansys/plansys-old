<?php

class StartController extends Controller {

    public function actionIndex() {
       // if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
            $connection = @fsockopen($_SERVER['HTTP_HOST'], '8981');
            if (is_resource($connection)) {
                echo "Port 8981 already used";
                fclose($connection);
            } else {
                $cmd = realpath(Yii::getPathOfAlias('webroot') . "/plansys/commands/shell/stream.js");

                if (substr(php_uname(), 0, 7) == "Windows") {
                    exec("plansys\commands\shell\psexec.exe -i -d node "  . $cmd);
                } else {
                    exec("nodejs " . $cmd . " > /dev/null &");
                }

                echo "Nfy Server Started";
            }
        //}
    }


}
