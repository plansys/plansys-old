<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}" expand-attributes="field.labelOptions" ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.layout == 'Vertical' ? 12 : 12 - field.labelWidth}}" style="padding-top:5px;">

        <label class="input-group {{field.itemLayout == 'Horizontal' ? 'inline' : ''}}" style="margin-right:5px;"
              ng-repeat="(key,value) in field.list track by $index">
            <input type="checkbox" name="{{field.name}}" id="{{field.name}}" value="{{key}}"> {{value}}
        </label>

    </div>
</div>