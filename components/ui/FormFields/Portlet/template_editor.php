<div class="portlet-container" style="width:{{field.width}}px;height:{{field.height}}px;border-width:1px;padding:3px;overflow:auto;">
    <div style="float:right;color:#999;margin:5px 10px;font-size:12px;text-align:right;margin-bottom:-25px;">
       <i class="fa fa-desktop"></i> {{field.name}}
    </div>
    <ol ui-tree-nodes ng-model="field.items">
        <li ng-repeat="field in field.items" 
            ui-tree-node ng-include="'FormTree'"
            ng-class="{
                        cpl: isPlaceholder(field), inline:field.displayInline}"></li>
    </ol>
</div>