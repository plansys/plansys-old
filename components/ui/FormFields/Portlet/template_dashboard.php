<div portlet class="portlet-container portlet-edit" ng-class="{maximized:maximized,showborder:!showBorder}" style="width:{{width}}px;height:{{height}}px;">
    <data name="width" class="hide"><?= $this->width; ?></data>
    <data name="height" class="hide"><?= $this->height; ?></data>
    <data name="top" class="hide"><?= $this->top; ?></data>
    <data name="left" class="hide"><?= $this->left; ?></data>
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

    <div class="portlet-info">
        <table>
            <tr>
                <td colspan="2"><u>{{name}}</u></td>
            </tr>
            <tr>
                <td>X</td>
                <td>: {{portlet.left| number:0 }} px</td>
            </tr>
            <tr>
                <td>Y</td>
                <td>: {{portlet.top| number:0 }} px</td>
            </tr>
            <tr>
                <td>W</td>
                <td>: {{portlet.width| number:0 }} px</td>
            </tr>
            <tr>
                <td>H</td>
                <td>: {{portlet.height| number:0 }} px</td>
            </tr>
        </table>

    </div>

    <div class="portlet-inner"><?= $this->renderItems ?></div>
</div>