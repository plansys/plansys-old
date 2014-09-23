<div ng-controller="NfyWidgetController">
    <div class = "properties-header">
        <div class="btn btn-xs btn-default pull-right">
            <i class="fa fa-check-square-o fa-lg"></i>
        </div>
        <i class = "fa fa-nm fa-newspaper-o"></i>&nbsp;
        Notifications
    </div>
    <div class="hide" id="nfy-uid"><?= Yii::app()->user->id ?></div>
    <div class="hide" id="nfy-data"><?= json_encode(Yii::app()->nfy->peek(Yii::app()->user->id, 25, NfyMessage::SENT)); ?></div>

    <div class="nfy-container widget-item-container" ng-if="!error">
        <div class="nfy-items">
            <div ng-repeat="item in $storage.nfy.items" class="nfy-item">
                <div class="nfy-item-sub">
                    <div class="nfy-item-left"></div>
                    <a href="{{ item.url}}" class="nfy-message" ng-bind-html="item.body.message"></a>
                    <div ng-if="item.body.notes" class="nfy-notes" > 
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
                    
                        <div class="nfy-date">{{ parseDate(item.created_on) | timeago}} </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>