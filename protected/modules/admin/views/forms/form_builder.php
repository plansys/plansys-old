
<div ui-content class="form-builder" style="top:0px;">
    <form class="form-horizontal" role="form">
        <div ui-tree="fieldsOptions">
            <ol ui-tree-nodes ng-model="fields">
                <li ng-class="{inline:field.displayInline}" ng-repeat="field in fields" ui-tree-node ng-include="'FormTree'"></li>
            </ol>
        </div>
    </form>
</div>
