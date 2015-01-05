<div portlet class="portlet-container" ng-class="{maximized:maximized,showborder:!showBorder}" style="width:{{width}}px;height:{{height}}px;">
    <data name="width" class="hide"><?= $this->width; ?></data>
    <data name="height" class="hide"><?= $this->height; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="zoomable" class="hide"><?= $this->zoomable; ?></data>
    <data name="showBorder" class="hide"><?= $this->showBorder; ?></data>
    
    <div class="portlet-buttons">
        <div ng-if="!maximized" class="btn btn-xs btn-default reset" ng-click="reset()">
            <i class="fa fa-rotate-left fa-fw"></i>
        </div>
        <div ng-if="!maximized && zoomable" class="btn btn-xs btn-default maximize" ng-click="maximize()">
            <i class="fa fa-desktop fa-fw"></i>
        </div>
        <div ng-if="maximized && zoomable" class="btn btn-xs btn-default minimize" ng-click="minimize()">
            <i class="fa fa-times fa-fw"></i>
        </div>
    </div>
    
    <div class="portlet-inner"><?= $this->renderItems ?></div>
</div>