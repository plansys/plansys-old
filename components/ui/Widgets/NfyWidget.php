<?php

class NfyWidget extends Widget {

    public $icon = "fa fa-newspaper-o fa-2x ";
    public $badge = '';

    public function includeJS() {
        return [
            'sse-client.js',
            'nfy-widget.js'
        ];
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

        ## cleanup url
        $url = $body['url'];
        if (Yii::app()->baseUrl != "" && strpos($url, Yii::app()->baseUrl) !== 0) {
            $url = Yii::app()->baseUrl . $url;
        }

        Yii::app()->controller->redirect($url);
    }

    public function actionMarkRead() {
        $sql = 'select id from p_nfy_subscriptions where subscriber_id = ' . Yii::app()->user->id;
        $sub_id = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = 'update p_nfy_messages set status = 2 where subscription_id = ' . $sub_id;
        Yii::app()->db->createCommand($sql)->execute();

        Yii::app()->controller->redirect(['widget/NfyWidget.history']);
    }

    public function actionHistory() {
        $sql = 'select id from p_nfy_subscriptions where subscriber_id = ' . Yii::app()->user->id;
        $_GET['sub_id'] = Yii::app()->db->createCommand($sql)->queryScalar();
        Yii::app()->controller->renderForm('application.forms.NfyMessagesIndex');
    }

    public function actionHistoryView($id) {
        $model = Yii::app()->controller->loadModel($id, "application.forms.NfyMessagesForm");
        Yii::app()->controller->renderForm("application.forms.NfyMessagesForm", $model);
    }
}
