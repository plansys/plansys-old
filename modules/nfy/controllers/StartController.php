<?php

class StartController extends Controller {

    public function actionIndex() {
        $connection = @fsockopen($_SERVER['HTTP_HOST'], '8981');
        if (is_resource($connection)) {
            echo "Port 8981 already used";
            fclose($connection);
        } else {
            $cmd = realpath(Yii::getPathOfAlias('webroot') . "/plansys/commands/shell/stream.js");

            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -accepteula -i -d node " . $cmd);
            } else {
                exec("nodejs " . $cmd . " > /dev/null &");
            }

            echo "Nfy Server Started";
        }
    }

}
