<div tag-field <?= $this->expandAttributes($this->options); ?>>
    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="value" class="hide"><?= $this->value ?></data>
    <data name="render_id" class="hide"><?= $this->renderID ?></data>
    <data name="dropdown" class="hide"><?= $this->dropdown ?></data>
    <data name="must_choose" class="hide"><?= $this->mustChoose ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="rel_model_class" class="hide"><?= $this->modelClass ?></data>
    <data name="params" class="hide"><?= json_encode($this->params) ?></data>
    <data name="value_mode" class="hide"><?= $this->valueMode; ?></data>
    <data name="delimiter" class="hide"><?= $this->valueModeDelimiter; ?></data>
    <data name="unique" class="hide"><?= $this->unique; ?></data>
    <data name="mapper_mode" class="hide"><?= $this->tagMapperMode; ?></data>
    <data name="field_options" class="hide"><?= json_encode($this->fieldOptions) ?></data>
    <!-- /info -->
    
    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?> <i ng-if="loading.length > 0" class="fa fa-refresh fa-spin"></i> 
        </label>
    <?php endif; ?>
    <!-- /label -->
    
    <!-- field -->
    <div class="<?= $this->fieldColClass ?>">
        <div class="form-control tf-container" ng-click="inputFocus()">
            <div class="tf-tag {{ disabled ? 'disabled' : ''}}" idx="{{$index}}" ng-repeat="(i,t) in tags" >
                <span class="tf-tag-text {{ t.editing ? 'editing' : '' }}">{{ t.label }}</span>
                <span ng-if="!disabled" class="tf-tag-remove" ng-click="removeTagFromValue(i)"></span>
                <input ng-if="!disabled" type="text" ng-blur="doneEditing(i,t,$event)" ng-focus="enableEdit(t, $event)" 
                    ng-click="$event.stopPropagation()"
                    style="display:none;" ng-model="t.label"
                    ng-keydown="inputKeydown($event, i)"
                    class="tf-input-edit {{ t.editing ? 'editing' : '' }}">
            </div>
            <input type="text" ng-if="!disabled"
                   ng-keyup="inputKeyup($event)"
                   ng-keydown="inputKeydown($event)" class="tf-input">
        </div>
        
    </div>
    <!-- /field -->
    <!-- error -->
    <div ng-if="errors[name]" class="alert error alert-danger">
        {{ errors[name][0]}}
    </div>
    <!-- /error -->
</div>
