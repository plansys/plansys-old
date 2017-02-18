<?php Yii::import('application.components.utility.Asset'); ?>
<?php Asset::registerJS($this->vpath . '.index'); ?>
<div ng-controller="Index">
    <div id="builder">
        <div ui-layout ng-class="{'active': active}" options="{ flow : 'column',dividerSize:1,disableToggle:true}">
            <div ui-layout-container 
                 size="{{layout.col1.width}}" 
                 min-size="{{layout.col1.minWidth}}"
                 class="col-1">
                <?php include("tree/tree.php"); ?>
            </div>
            <div ui-layout-container
                 class="col-2"
                 size="{{layout.col2.width}}" >
                <?php include("tabs/tabs.php"); ?> 
            </div>
        </div>
    </div>
</div>