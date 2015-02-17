<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div ng-if="field.prefix != '' || field.postfix != ''" class="input-group">


        <div style="float:left;">
            <input type="checkbox" 
                   ng-model="value" ng-change="update()" ui-switch checked />
        </div>
            <div ng-if="value" class="label label-success switchery-label" style="background:#aad596;">ON</div>
            <div ng-if="!value" class="label label-default switchery-label" style="background:#ccc;">OFF</div>
      

        </div>

        <input ng-if="field.prefix == '' && field.postfix == ''" 
               type="{{field.fieldType}}"
               id='{{field.name}}' name='{{field.name}}' class="form-control" 
               expand-attributes="field.options" value="{{field.value}}">

    </div>
</div>