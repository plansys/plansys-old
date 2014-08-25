
<table style="width:100%;">
    <tr>
        <td ng-if='field.totalColumns >= 1'
            class="form-builder-column first {{field.totalColumns==1?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
            <ol ui-tree-nodes ng-model="field.column1">
                <li  ng-repeat="field in field.column1" ui-tree-node ng-include="'FormTree'" ng-class="{
                            cpl: isPlaceholder(field), inline:field.displayInline
                            }"></li>
            </ol>
        </td>
        <td ng-if='field.totalColumns >= 2'
            class="form-builder-column {{field.totalColumns==2?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
            <ol ui-tree-nodes ng-model="field.column2">
                <li  ng-repeat="field in field.column2" ui-tree-node ng-include="'FormTree'" ng-class="{
                            cpl: isPlaceholder(field), inline:field.displayInline
                            }"></li>
            </ol>
        </td>
        <td ng-if='field.totalColumns >= 3'
            class="form-builder-column {{field.totalColumns==3 ?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
            <ol ui-tree-nodes ng-model="field.column3">
                <li  ng-repeat="field in field.column3" ui-tree-node ng-include="'FormTree'" ng-class="{
                            cpl: isPlaceholder(field), inline:field.displayInline
                            }"></li>
            </ol>
        </td>
        <td ng-if='field.totalColumns >= 4'
            class="form-builder-column {{field.totalColumns==4?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
            <ol ui-tree-nodes ng-model="field.column4">
                <li  ng-repeat="field in field.column4" ui-tree-node ng-include="'FormTree'" ng-class="{
                            cpl: isPlaceholder(field), inline:field.displayInline
                            }"></li>
            </ol>
        </td>
        <td ng-if='field.totalColumns >= 5'
            class="form-builder-column {{field.totalColumns==4?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
            <ol ui-tree-nodes ng-model="field.column5">
                <li ng-repeat="field in field.column5" ui-tree-node ng-include="'FormTree'" ng-class="{
                            cpl: isPlaceholder(field), inline:field.displayInline
                            }"></li>
            </ol>
        </td>
    </tr>
</table>
