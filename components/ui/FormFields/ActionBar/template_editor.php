<div class="action-bar-editor">
    <div class="ab-editor-title-bar">
        <span class="title" dynamic="field.title"></span>
    </div>
    <div class="ab-editor-link-bar">
        <ol ui-tree-nodes ng-model="field.linkBar">
            <li ng-repeat="field in field.linkBar" 
                ui-tree-node ng-include="'FormTree'" 
                ng-class="{cpl: isPlaceholder(field), inline:field.displayInline}"></li>
        </ol>
    </div>
    <div class="clearfix"></div>
    <div class="ab-editor-tab" ng-if="field.showSectionTab == 'Yes'">
        <a href="#" class="active">{{field.firstTabName}}</a>
        <div class="clearfix"></div>
    </div>
</div>