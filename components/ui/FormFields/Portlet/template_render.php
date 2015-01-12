<div portlet name="<?= $this->name; ?>" class="portlet-container" ng-class="{maximized:maximized,showborder:!showBorder, editing: editing}" style="width:{{width}}px;height:{{height}}px;">
    <data name="top" class="hide"><?= $this->top; ?></data>
    <data name="left" class="hide"><?= $this->left; ?></data>
    <data name="width" class="hide"><?= $this->width; ?></data>
    <data name="height" class="hide"><?= $this->height; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="zoomable" class="hide"><?= $this->zoomable; ?></data>
    <data name="showBorder" class="hide"><?= $this->showBorder; ?></data>

    <div class="portlet-buttons" ng-class="{editing:editing}">
        <div ng-if="!maximized && editing" class="btn btn-xs btn-default reset" ng-click="reset()">
            <i class="fa fa-rotate-left fa-fw"></i> 
        </div>
        <div ng-if="!maximized && editing" class="btn btn-xs btn-default reset" ng-click="doneEdit()">
            <i class="fa fa-check fa-fw"></i> 
        </div>
        <div ng-if="!maximized && !editing" class="btn btn-xs btn-default maximize" ng-click="edit()">
            <i class="fa fa-pencil fa-fw"></i>
        </div>
        <div ng-if="!maximized && zoomable" class="btn btn-xs btn-default maximize" ng-click="maximize()">
            <i class="fa fa-desktop fa-fw"></i>
        </div>
        <div ng-if="maximized && zoomable" class="btn btn-xs btn-default minimize" ng-click="minimize()">
            <i class="fa fa-times fa-fw"></i>
        </div>
    </div>

    <div class="portlet-title"><?= $this->title; ?></div>
    <div ng-if="editing" class="portlet-overlay"></div>
    <div class="portlet-inner <?= $this->title == '' ? '' : 'with-title'?>"><div class="portlet-inner-content"><?= $this->renderItems ?></div></div>
</div>