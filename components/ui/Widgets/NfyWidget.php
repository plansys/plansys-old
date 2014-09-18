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

    public function actionPeek() {
        var_dump(Yii::app()->nfy->peek(Yii::app()->user->id, 5));
    }

    public function actionStream() {
    }

}
