<div ng-if="isPlaceholder(field)" class="column-placeholder" style="min-height: 50px;">
    <div ui-tree-handle class="column-placeholder-handle"></div>
    {{ refreshColumnPlaceholder()}}
</div>
<div ng-if="!isPlaceholder(field)" 
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
                    <span ng-switch-default>
                        {{field.type}}
                    </span>
                </span>
            </div>

            <div ui-tree-handle class='field-move btn btn-default btn-sm'>
                <i class='fa fa-arrows'></i>
                <span class="field-info-text">Move</span> 
            </div>
            <div ng-click="moveToPrev(this)"  class='field-move-left btn btn-default btn-sm'>
                <i class='fa fa-chevron-left'></i>
            </div>
            <div ng-click="moveToNext(this)" class='field-move-right btn btn-default btn-sm'>
                <i class='fa fa-chevron-right'></i>
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
        <span style="font-weight:normal">[{{field.name}}]</span>
    </div>
    <div class="form-field-content"
         ng-include="Yii.app.createUrl('dev/forms/renderTemplate', {class: field.type})"
         onload="relayout(field.type)"></div>

    <div class="clearfix"></div>
</div>