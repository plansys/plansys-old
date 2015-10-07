<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <!-- field -->
        <div class="toggle-switch-field">
            <div class="toggle-switchery">
                <input type="checkbox" ng-model="field.value" ui-switch checked/>
            </div>
            <div ng-if="valueCheckbox" class="label label-success switchery-label">
                {{ field.onLabel}}
            </div>
            <div ng-if="!valueCheckbox" class="label label-default switchery-label">
                {{ field.offLabel}}
            </div>
        </div>
        <!-- /field -->
    </div>
</div>