<?php

class NfyCommand extends CConsoleCommand {

    public function actionReceive($id) {

        $list = Yii::app()->nfy->receive($id, 5);
        if (count($list) > 0) {
            echo 'data: ' . json_encode($list) . "\n\n";
        }
    }

}
