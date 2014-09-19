<?php

class NfyCommand extends CConsoleCommand {

    public function actionReceive($id) {

        $list = Yii::app()->nfy->receive($id);
        if (count($list) > 0) {
            echo json_encode($list) . "\n\n";
        }
    }

}
