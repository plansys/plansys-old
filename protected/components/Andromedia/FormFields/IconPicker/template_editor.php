<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}}">
        <div class="btn btn-default">
            <i class="fa fa-smile-o fa-lg"></i>
        </div>
    </div>
</div>