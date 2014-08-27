<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}"
     style="{{field.options.style}}" >
    <label for="{{field.name}}" expand-attributes="field.labelOptions" ng-show="field.label != ''"
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{ field.layout == 'Vertical' ? 12 : 12 - field.labelWidth}}" style="padding-top:5px;">

        <div ng-if="field.itemLayout == 'ButtonGroup'">
            <div class="btn-group">
                <label class="radio-btn btn btn-sm btn-default" id="{{field.name}}" ng-value="value" 
                       ng-repeat="(key, value) in field.list  track by $index">{{value}}</label>
            </div>
        </div>
        <div ng-if="field.itemLayout != 'ButtonGroup'">
            <label class="input-group {{field.itemLayout == 'Horizontal' ? 'inline' : ''}}" style="margin-right:5px;"
                   ng-repeat="(key, value) in field.list  track by $index">
                <input type="radio" name="{{field.name}}" id="{{field.name}}" value="{{key}}"> {{value}}
            </label>
        </div>
    </div>
</div>