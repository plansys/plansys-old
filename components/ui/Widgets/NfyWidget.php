<?php

class NfyWidget extends Widget {

    public $icon = "fa fa-newspaper-o fa-2x ";
    public $badge = '';

    public function includeJS() {
        return array(
            'nfy-widget.js'
        );
    }

    public function actionRead($nid) {
        $nfy = Yii::app()->nfy->read($nid, true);
        $now = new DateTime('now');
        $body = json_decode($nfy->body, true);
        if (!@$body['persist']) {
            $nfy->status = NfyMessage::READ;
            $nfy->read_on = $now->format('Y-m-d H:i:s');
            $nfy->save();
        } else {
            $body['url'] .= '&nfyid=' . $nfy->id;
        }
        Yii::app()->controller->redirect($body['url']);
    }

    public function actionNotify() {
        Yii::app()->nfy->send(array(
            'url' => Yii::app()->controller->createUrl('/dev/user/update/', array('id' => Yii::app()->user->id)),
            'message' => 'Pembuatan Rencana Kerja (6 Lubang) daerah bangka ',
            'notes' => 'tolong sekalian tambahin kerjaan nya ya...'
            ), array(
            'id' => '1'
        ));
    }

    public function actionPeek() {
        var_dump(Yii::app()->nfy->peek(Yii::app()->user->id, 5));
    }

}
