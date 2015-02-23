<span link-btn ng-if="field.buttonType != 'not-btn'" 
      class="link-btn on-editor btn btn-{{field.buttonType}} {{field.buttonSize}}">
    <i style="margin-right:4px;" ng-if="field.icon" class="fa fa-{{field.icon}}"></i>
    {{field.label}}
</span>
<a link-btn ng-if="field.buttonType == 'not-btn'" class="link-btn on-editor ">
    <i style="margin-right:4px;" ng-if="field.icon" class="fa fa-{{field.icon}}"></i>
    {{field.label}}
</a>
