<div ng-init="initField(field)" class="field-content">
    <div ng-if="field.$isPlaceholder" class="column-placeholder" style="min-height: 50px;">
        <div ui-tree-handle class="column-placeholder-handle" ng-init="increaseLoadedField()"></div>
        {{ refreshColumnPlaceholder()}}
    </div>
    <div ng-else
         ng-click="select(this, $event);"
         depth="{{this.$nodeScope.depth()}}" 
         class='form-field field'>
        <div class='field-info' >
            <div class="btn-group pull-right">
                <div class='field-select btn btn-default btn-sm'>
                    <span ng-switch on="field.type">
                        <i ng-switch-when="ColumnField" class="fa fa-columns"></i>
                        <i ng-switch-default class="fa fa-hand-o-up"></i>
                    </span>
                    <span class="field-info-text" ng-switch on="field.type">
                        <span ng-switch-when="ColumnField"> 
                            Select Columns
                        </span>
                        <span ng-switch-when="LinkButton"><b>Select</b></span> 
                        <span ng-switch-default>
                            {{field.type}}
                        </span>
                    </span>
                </div>
                <div ui-tree-handle class='field-move btn btn-default btn-sm'>
                    <i class='fa fa-arrows' style="margin:0px;"></i>
                    <span class="field-info-text" style="display:none;"></span> 
                </div>
                <div ui-tree-handle 
                     ng-mouseover='prepareCloneField(this)' 
                     ng-mouseleave='cancelCloneField()'
                     class='btn btn-default btn-sm'>
                    <i class='fa fa-copy'></i>
                </div>
            </div>
        </div>
        <div fname="{{ generateIdentity(field)}}" ng-class="'d-' + generateIdentity(field)"
             class="duplicate label label-warning ng-hide" 
             style="position:absolute;"><i class="fa fa-warning"></i> Duplicate 
            <span style="font-weight:normal">[{{field.name}}]</span>
        </div>
        <div class="form-field-content"
             ng-include="Yii.app.createUrl('dev/forms/renderTemplate', {class: field.type})"
             onload="relayout(field)"></div>

        <div class="clearfix"></div>
    </div>
</div>