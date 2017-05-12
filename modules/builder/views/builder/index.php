<?php Yii::import('application.components.utility.Asset'); ?>
<?php Asset::registerJS($this->vpath . '.index'); ?>
<script type="text" id="chat-data">
     <?php echo json_encode($chat); ?>
</script>
<div ng-controller="Index" style="overflow:hidden" >
    <?php
        FormBuilder::renderUI('WebSocketClient', [
            'name' => 'ws',
            'ctrl' => 'builder/collab'
        ]);
    ?>
    <div id="builder" layout-width="<?= $width; ?>" uid="<?= Yii::app()->user->id; ?>">
        <div ui-layout ng-class="{'active': active}" 
             options="{flow : 'column',dividerSize:0,disableToggle:true}">
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