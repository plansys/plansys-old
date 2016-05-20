<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label expand-attributes="field.labelOptions" 
           ng-show="field.label != ''" 
           class="col-sm-{{field.labelWidth}} control-label"
           ng-bind-html="field.label"
           ></label>
    <div class="col-sm-{{field.fieldWidth}}">
        <textarea rows="{{field.fieldHeight}}"
                  class="form-control {{field.fieldHeight > 0 ? 'force-rows' : ''}}"
                  expand-attributes="field.options" >{{field.value}}</textarea>
    </div>
</div>