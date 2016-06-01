<div ng-controller="NfyWidgetController">
    <div class="properties-header">
        <a href="{{ Yii.app.createUrl('/widget/NfyWidget.history') }}" class="btn btn-xs btn-default pull-right">
            <i class="fa fa-history"></i> History
        </a>

        <i class="fa fa-nm fa-newspaper-o"></i>&nbsp;
        Notifications
    </div>
    <div class="hide" id="nfy-uid"><?= Yii::app()->user->model->subscription['id'] ?></div>
    <div class="hide"
         id="nfy-data"><?= json_encode(Yii::app()->nfy->peek(Yii::app()->user->id, 25, NfyMessage::SENT)); ?></div>

    <div class="nfy-container widget-item-container" ng-if="!error">
        <div class="nfy-items">
            <?php if (Yii::app()->user->model->email == ""): ?>
                <div class="alert alert-warning"
                     style="border-radius:0px;padding:10px;font-size:13px;text-align:center;margin:0px;">
                    <a style="color:#8a6d3b;" href="{{ Yii.app.createUrl('/sys/profile/index', {e:'email'}) }}">
                        Mohon lengkapi e-mail Anda untuk menerima notifikasi via e-mail.
                    </a>
                </div>
            <?php endif; ?>
            <div ng-repeat="item in $storage.nfy.items" class="nfy-item">
                <div class="nfy-item-sub">
                    <div class="nfy-item-left"></div>
                    <a href="{{ item.url}}" class="nfy-message" ng-bind-html="item.body.message"></a>

                    <div ng-if="item.body.notes" class="nfy-notes">
                        <i class="fa fa-quote-left pull-left" style="margin-top:3px;"></i>

                        <div style="margin-left:20px;" ng-bind-html="item.body.notes"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="nfy-info">
                        By:
                        <a href="#" class="nfy-sender">
                            <span class="nfy-name">{{ item.sender_name}}</span>
                            <span class="nfy-role">{{ item.sender_role}}</span>
                        </a>

                        <div class="nfy-date">{{ parseDate(item.created_on) | timeago}}</div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div ng-if="$storage.nfy.items.length == 0"
                 style="color:#ccc;text-align:center;line-height:35px;padding:50px 0px;">
                <i class="fa fa-send-o fa-4x"></i><br/>
                <b>Notification Empty</b>
            </div>
        </div>
    </div>
</div>