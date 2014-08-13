<div class="field-box" style="margin:0px;">
    <div class="properties-header">

        <i class="fa fa-terminal"></i>
        {{ field.label }}
        <div class='btn btn-xs pull-right' style="color:green;">
            <i class="fa fa-check-circle"></i> Valid
        </div>
    </div>

    <div ng-hide="field.desc ==''" class="field-box-desc" ng-bind-html='field.desc'>
    </div>
</div>