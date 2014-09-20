<span ng-if="field.renderInEditor == 'Yes'" dynamic="field.value"></span>
<div ng-if="!field.renderInEditor || field.renderInEditor == 'No'" 
     class="text-editor-container">
    <div class="text-editor-preview">{{ field.value}}</div>
    <div class="text-editor-more">
        <div style="float:left;margin:-6px 2px 0px 0px;">... </div>
        <i style="float:left;margin-top:3px;" class="fa fa-angle-down"></i>
    </div>
</div>