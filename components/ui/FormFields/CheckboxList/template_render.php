<div check-box-list convertToString="<?= $this->convertToString ?>"
     <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?> <?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" style="padding-top:5px;">
        <!-- data -->
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="mode" class="hide"><?= $this->mode; ?></data>
        <data name="selected" class="hide"><?= json_encode($this->value); ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->formattedList); ?></data>
        <data name="rel_info" class="hide"><?= json_encode($this->getRelationInfo()); ?></data>
        <!-- /data -->

        <?php if ($this->mode == 'Relation'): ?>
            <data name="delete_data" class="hide"><?= @json_encode($this->deleteData); ?></data>
            <input name="<?= $this->getPostName('Insert'); ?>" type="hidden" value="{{ insertData | json }}"/>
            <input name="<?= $this->getPostName('Delete'); ?>" type="hidden" value="{{ deleteData | json }}"/>
        <?php endif; ?>
        
        <!-- field -->
        <span ng-repeat="item in formList track by $index" class="input-list-outer">
            <label <?= $this->expandAttributes($this->fieldOptions) ?>>
                <input <?= $this->expandAttributes($this->fieldOptions) ?> 
                    type="checkbox" id="<?= $this->renderID ?>_{{item.value}}"
                       name="<?= $this->name ?>[{{item.value}}]"
                       ng-checked="isChecked(item.value)"
                       ng-click="updateItem(item.value)"
                       /> {{ item.text }}
            </label>
        </span>

        <?php if ($this->convertToString == "Yes" && $this->mode == 'Default'): ?>
            <input type="text" class="invisible"
                   ng-model="selectedText" id="<?= $this->renderID ?>"
                   name="<?= $this->renderName ?>" value='<?= $this->value ?>'/>
        <?php endif; ?>
        <!-- /field -->

        <!-- error -->
        <div ng-if="errors[name]" class="alert error alert-danger">
            {{ errors[name][0] }}
        </div>
        <!-- /error -->
    </div>
</div>