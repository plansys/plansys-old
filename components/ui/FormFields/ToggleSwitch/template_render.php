<div toggle-switch <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">

        <!-- data -->
        <data name="name" class="hide" ><?= $this->name ?></data>
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="options" class="hide"><?= @json_encode($this->options) ?></data>
        <data name="on_label" class="hide"><?= $this->onLabel ?></data>
        <data name="off_label" class="hide"><?= $this->offLabel ?></data>
        <!-- /data -->

        <!-- field -->
        <div class="toggle-switch-field">
            <div class="toggle-switchery">
                <input type="checkbox" <?= $this->expandAttributes($this->fieldOptions) ?>
                       ng-model="valueCheckbox" ng-change="update()" ui-switch checked/>
                <input type="hidden" name="<?= $this->renderName ?>" value="{{ value}}" />
            </div>
            <div ng-if="valueCheckbox" ng-click="switch ()"
                 class="label label-success switchery-label <?= $this->switcheryLabelClass; ?>">
                     <?= $this->onLabel; ?>
            </div>
            <div ng-if="!valueCheckbox" ng-click="switch ()"
                 class="label label-default switchery-label <?= $this->switcheryLabelClass; ?>">
                     <?= $this->offLabel; ?>
            </div>
        </div>
        <!-- /field -->

        <!-- error -->
        <div ng-if="errors[name]" class="alert error alert-danger">
            {{ errors[name][0]}}
        </div>
        <!-- /error -->
    </div>
</div>