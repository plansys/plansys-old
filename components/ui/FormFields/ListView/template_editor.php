<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">

        <div class="list-view-item" style='margin-bottom:-1px;'>
            <div style="float:right;margin-top:7px;">
                <div  class="list-view-item-remove btn btn-xs">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div class='list-view-item-container' style="opacity:1;">
                <div style="height:31px;"></div>
            </div>
        </div>
        <div class="btn list-view-add btn-default btn-sm"><i class="fa fa-nm fa-plus"></i> <b>Add</b></div>
    </div>
</div>