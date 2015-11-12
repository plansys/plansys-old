<div radio-button-list <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?><?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" style="padding-top:5px;">
        <!-- data -->
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list) ?></data>
        <!-- /data -->

        <!-- field -->
        <span ng-repeat="(val, text) in formList track by $index">
            <label <?= $this->expandAttributes($this->fieldOptions) ?>>
                <input type="radio" id="<?= $this->renderID ?>_{{val}}"
                       ng-checked="value == val"
                       ng-click="update(val)"
                       /> {{ text}}
            </label>
        </span>
        <input type="hidden" name="<?= $this->renderName ?>" value="{{ value}}" />

        <!-- /field -->

        <!-- error -->
        <div class="clearfix"></div>
        <div ng-if="errors[name]" class="alert error alert-danger">
            {{ errors[name][0]}}
        </div>
        <!-- /error -->

    </div>
</div>