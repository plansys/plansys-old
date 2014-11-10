
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
    </tr>
</table>
