<div ng-controller="NfyWidgetController">
    <div class = "properties-header">
        <div class="btn btn-xs btn-default pull-right">
            <i class="fa fa-check-square-o fa-lg"></i>
        </div>
        <i class = "fa fa-nm fa-newspaper-o"></i>&nbsp;
        Notifications
    </div>
    <div class="hide" id="nfy-uid"><?= Yii::app()->user->id ?></div>
    <div class="hide" id="nfy-data"><?=
        json_encode(Yii::app()->nfy->peek(Yii::app()->user->id, 25, NfyMessage::SENT));
        ?></div>

    <div class="nfy-container" ng-if="!error">
        <div class="nfy-items">
            <a href="{{ item.body.url }}" ng-repeat="item in $storage.nfy.items" class="nfy-item">
                <div class="nfy-name">{{ item.subscriber_name}}</div>
                <div class="nfy-message" ng-bind-html="item.body.message"></div>
                <div class="nfy-info">
                    <div class="nfy-date">{{ parseDate(item.created_on) | timeago}} </div>
                    <div class="nfy-role label label-default">{{ item.subscriber_role}}</div>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>