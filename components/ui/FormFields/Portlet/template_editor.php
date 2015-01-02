<div class="portlet-container" style="width:300px;height:200px;">
    <ol ui-tree-nodes ng-model="field.items">
        <li ng-repeat="field in field.items" 
            ui-tree-node ng-include="'FormTree'"
            ng-class="{cpl: isPlaceholder(field), inline:field.displayInline}"></li>
    </ol>
</div>