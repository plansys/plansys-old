<?php Yii::import('application.components.utility.Asset'); ?>
<div ng-controller="Index">
    <div id="builder">
        <div ui-layout options="{ flow : 'column',dividerSize:1,disableToggle:true}">
            <div id="1st-col" 
                 ui-layout-container 
                 size="{{col1.layout.width}}" 
                 min-size="{{col1.layout.minWidth}}"
                 resizable="col1.layout.resizeable"
                 collapsed="col1.layout.collapsed" 
                 class="sidebar">
                <?php include("col1.php"); ?>

            </div>
            <div id="2nd-col"
                 ui-layout-container
                 size="{{col2.width}}" 
                 resizable="col2.resizeable"
                 collapsed="col2.collapsed">
                
            </div>
            <div id="3rd-col" 
                 ui-layout-container 
                 size="{{col3.width}}" 
                 resizable="col3.resizeable"
                 collapsed="col3.collapsed">

            </div>
        </div>
    </div>
</div>