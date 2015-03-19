<?php

class NfyCommand extends CConsoleCommand {

    public function actionReceive($id) {
        $enableNotif = Setting::get("notif.enable");
        if(!!$enableNotif){
            $list = Yii::app()->nfy->receive($id);
            if (count($list) > 0) {
                echo json_encode($list) . "\n\n";
            }
        }
    }

}
