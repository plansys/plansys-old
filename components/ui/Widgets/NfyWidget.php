<?php

class NfyWidget extends Widget {

    public $icon = "fa fa-newspaper-o fa-2x ";
    public $badge = '';

    public function includeJS() {
        return array(
            'nfy-widget.js'
        );
    }

    public function actionNotify() {
        $var = json_encode(array(
            'url' => Yii::app()->controller->createUrl('/dev/user/update/', array('id' => Yii::app()->user->id)),
            'message' => 'Pergi ke halaman update user ' . Yii::app()->user->model->fullname
        ));
        Yii::app()->nfy->send($var, array(
            'role' => 'admin'
        ));

        echo $var;
    }

    public function actionPeek() {
        var_dump(Yii::app()->nfy->peek(Yii::app()->user->id, 5));
    }

    public function actionStream() {
        
    }

}
