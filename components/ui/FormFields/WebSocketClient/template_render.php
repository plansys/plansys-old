<div web-socket-state>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="port" class="hide"><?= $this->getPort(); ?></data>
    <data name="config" class="hide"><?php 
        if (Yii::app()->user->isGuest) {
            echo json_encode([
                'tid' => $this->ctrl,
                'uid' => '',
                'sid' => Yii::app()->getSession()->getSessionId(),
                'cid' => ''
            ]);
        } else {
            echo json_encode([
                'tid' => $this->ctrl,
                'uid' => Yii::app()->user->id,
                'sid' => Yii::app()->getSession()->getSessionId(),
                'cid' => ''
            ]);
        }
    ?></data>
</div>