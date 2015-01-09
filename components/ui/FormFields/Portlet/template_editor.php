<div style="min-height:30px;overflow:auto;border:1px dashed #ddd;overflow-x:hidden;">
    <div style="float:left;color:#999;margin:0px 5px;font-size:12px;text-align:right;margin-bottom:-25px;">
       <i class="fa fa-desktop"></i> {{field.name}}
    </div>
    <ol ui-tree-nodes ng-model="field.items" style="padding:0px;">
        <li ng-repeat="field in field.items" 
            ui-tree-node ng-include="'FormTree'"
            ng-class="{cpl: isPlaceholder(field), inline:field.displayInline}"></li>
    </ol>
</div>