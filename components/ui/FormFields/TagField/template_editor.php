<div expand-attributes="field.options" 
class="form-group form-group-sm {{ field.listViewLayout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.listViewLayout == 'Vertical' ? 12 :  field.labelWidth}} control-label">
        {{field.label}}
        </label>
    <div class="col-sm-{{field.fieldWidth}}">
        
        <input type="{{field.fieldType}}"
               id='{{field.name}}' name='{{field.name}}' class="form-control" 
               expand-attributes="field.options" value="{{field.value}}">

    </div>
</div>