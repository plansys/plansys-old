<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}" 
           expand-attributes="field.labelOptions" 
           ng-show="field.label != ''" 
           class="col-sm-{{field.labelWidth}} control-label"
           ng-bind-html="field.label"
           ></label>
    <div class="col-sm-{{field.fieldWidth}}">
        <textarea id='{{field.name}}' name='{{field.name}}' rows="{{field.fieldHeight}}"
                  class="form-control" expand-attributes="field.options" >{{field.value}}</textarea>
    </div>
</div>