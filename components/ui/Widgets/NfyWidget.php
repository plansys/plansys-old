<?php

class NfyWidget extends Widget {

    public $icon = "fa fa-newspaper-o fa-2x ";
    public $badge = '';

    public function includeJS() {
        return array(
            'nfy-widget.js'
        );
    }

    public function actionSubscribe() {
        $uid = Yii::app()->user->id;
        $role = Yii::app()->user->role;
        Yii::app()->nfy->subscribe($uid, $uid, array('uid_' . $uid, 'role' => $role));
    }

    public function actionNotify() {
        Yii::app()->nfy->send('test', array(
            'role_dev'
        ));
    }

    public function actionRead() {
        var_dump(Yii::app()->nfy->receive(Yii::app()->user->id, 5));
    }

    public function actionPeek() {
        var_dump(Yii::app()->nfy->peek(Yii::app()->user->id, 5));
    }

    public function actionStream() {
        ob_end_clean();
        header("Content-Type: text/event-stream\n\n");

        $counter = rand(1, 10);
        set_time_limit(2);
        error_reporting(0);

        while (1) {
            $list = Yii::app()->nfy->receive(Yii::app()->user->id, 5);
            if (count($list) > 0) {
                echo 'data: ' . json_encode($list) . "\n\n";
            }
            
            ob_flush();
            flush();
            sleep(1);
        }
    }

}
