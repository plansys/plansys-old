
<div expand-attributes="field.options" class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <label for="{{field.name}}"  expand-attributes="field.labelOptions"  ng-show="field.label != ''" 
           class="col-sm-{{field.layout == 'Vertical' ? 12 :  field.labelWidth}} control-label">{{field.label}}</label>
    <div class="col-sm-{{field.fieldWidth}}">
        <div class=" nsg-editor"> 
            <span class="nsg-tags nsg-tags-before">
                <span class="nsg-tag">Tags<span class="nsg-tag-remove"></span>

                </span>

            </span>
            <span class="nsg-tags nsg-tags-after"></span>
        </div>
    </div>
</div>