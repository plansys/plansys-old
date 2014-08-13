<div class='form-field field {{isPlaceholder(field) ? "column-placeholder" : field.type}}'>

    <div ng-click="select(this);" depth="{{this.$nodeScope.depth()}}"  
         class='field-info' ng-if="!isPlaceholder(field)" >
        <div class="btn-group">
            <div class='btn btn-default btn-sm'>
                <span ng-switch on="field.type">
                    <i ng-switch-when="ColumnField" class="fa fa-columns"></i>
                    <i ng-switch-default class="fa fa-hand-o-up"></i>
                </span>
                <span ng-switch on="field.type">
                    <span ng-switch-when="ColumnField"> 
                        Select Columns
                    </span>
                    <span ng-switch-default></span>
                </span>
            </div>

            <div ui-tree-handle class='btn btn-default btn-sm'>
                <i class='fa fa-arrows'></i>
                Move 
            </div>
            <div ui-tree-handle 
                 ng-mouseover='prepareCloneField(this)' 
                 ng-mouseleave='cancelCloneField()'
                 class='btn btn-default btn-sm'>
                <i class='fa fa-copy'></i>
            </div>
        </div>
    </div>
    <div fname="{{field.name}}" ng-class="'d-' + formatName(field.name)"
         class="duplicate label label-warning ng-hide" 
         style="position:absolute;"><i class="fa fa-warning"></i> Duplicate
    </div>
    <div ui-tree-handle ng-if="isPlaceholder(field)" class="column-placeholder-handle"></div>
    <div ng-click="select(this);" class="form-field-content"
         ng-include="Yii.app.createUrl('admin/forms/renderTemplate', {class: field.type})"
         onload="relayout(field.type)"></div>


    <table style="width:100%;" ng-if='field.type == "SectionField"'>
        <tr>
            <td class="form-builder-column" style='width:100%'>
                <ol ui-tree-nodes ng-model="field.content">
                    <li ng-repeat="field in field.content" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
        </tr>
    </table>

    <table style="width:100%;" ng-if='field.type == "ColumnField"'>
        <tr>
            <td ng-if='field.totalColumns >= 1'
                class="form-builder-column first {{field.totalColumns==1?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
                <ol ui-tree-nodes ng-model="field.column1">
                    <li ng-repeat="field in field.column1" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
            <td ng-if='field.totalColumns >= 2'
                class="form-builder-column {{field.totalColumns==2?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
                <ol ui-tree-nodes ng-model="field.column2">
                    <li ng-repeat="field in field.column2" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
            <td ng-if='field.totalColumns >= 3'
                class="form-builder-column {{field.totalColumns==3 ?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
                <ol ui-tree-nodes ng-model="field.column3">
                    <li ng-repeat="field in field.column3" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
            <td ng-if='field.totalColumns >= 4'
                class="form-builder-column {{field.totalColumns==4?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
                <ol ui-tree-nodes ng-model="field.column4">
                    <li ng-repeat="field in field.column4" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
            <td ng-if='field.totalColumns >= 5'
                class="form-builder-column {{field.totalColumns==4?'last':''}}" style='width:{{100 / field.totalColumns}}%'>
                <ol ui-tree-nodes ng-model="field.column5">
                    <li ng-repeat="field in field.column5" ui-tree-node ng-include="'FormTree'" ng-class="{
                                cpl: isPlaceholder(field)
                            }"></li>
                </ol>
            </td>
        </tr>
    </table>

    <div class="clearfix"></div>
</div>