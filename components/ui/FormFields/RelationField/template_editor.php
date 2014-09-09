<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}" ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label"
           expand-attributes="field.labelOptions">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div class="btn btn-sm btn-default btn-block" style="text-align:right;">
            <span class="caret"></span>
        </div>
    </div>
</div>