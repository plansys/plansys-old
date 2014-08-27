<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div ng-if="field.fieldTemplate == 'default'">
            <div class="list-view-item">

                <input class="form-control" type="text">
                <div class="list-view-item-remove input-group-addon btn">
                    <i class="fa fa-times"></i>
                </div>

            </div>
        </div>
        <div class="btn list-view-add btn-default btn-sm"><i class="fa fa-nm fa-plus"></i> <b>Add</b></div>
    </div>
</div>