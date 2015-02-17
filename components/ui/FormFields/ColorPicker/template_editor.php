<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div ng-if="field.prefix != '' || field.postfix != ''" class="input-group">
            <!-- prefix -->
            <span ng-if="field.prefix != '' && field.prefix" class="input-group-addon">
                {{ field.prefix}}
            </span>

            <!-- value -->
            <input type="{{field.fieldType}}" id='{{field.name}}' name='{{field.name}}' class="form-control" 
                   expand-attributes="field.fieldOptions" value="{{field.value}}">

            <!-- postfix -->
            <span ng-if="field.postfix != '' && field.postfix" class="input-group-addon">
                {{ field.postfix}}
            </span>
        </div>
        
        <input ng-if="field.prefix == '' && field.postfix == ''" 
               type="{{field.fieldType}}"
               id='{{field.name}}' name='{{field.name}}' class="form-control" 
               expand-attributes="field.options" value="{{field.value}}">

    </div>
</div>