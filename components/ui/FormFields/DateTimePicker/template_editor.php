<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div class="input-group" expand-attributes="field.fieldOptions" >
            <!-- value -->
            <input type="text" id='{{field.name}}' name='{{field.name}}' class="form-control" 
                   value="{{field.value}}">
            
            <span class="input-group-btn">
                <button type="button" class="btn btn-sm btn-default">
                    <i class="glyphicon glyphicon-calendar"></i>
                </button>
            </span>
        </div>
    </div>
</div>