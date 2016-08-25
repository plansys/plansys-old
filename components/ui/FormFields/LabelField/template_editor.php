<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div  id='{{field.name}}' class='label-field-value' name='{{field.name}}'>&nbsp;</div>
    </div>
</div>