
<div ui-content class="form-builder" style="top:0px;padding-top:17px;">
    <form class="form-horizontal" role="form" style="padding-bottom:500px;">
        <div oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
            <div class='field-tree' ui-tree="fieldsOptions">
                <ol ui-tree-nodes ng-model="fields">
                    <li ng-repeat="field in fields"  
                        ng-class="{inline:field.displayInline || field.display == 'inline'}" 
                        class="{{field.type}}"
                        ui-tree-node ng-include="'FormTree'"></li>
                </ol>
            </div>
        </div>
    </form>
</div>
