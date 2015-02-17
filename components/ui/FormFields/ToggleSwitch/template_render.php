    
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
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <!-- /data -->

        <!-- field -->
        <div style="float:left;">
            <input type="checkbox" <?= $this->expandAttributes($this->fieldOptions) ?>
                   ng-model="value" ng-change="update()" ui-switch checked />
        </div>
        <div ng-if="value" class="label label-success switchery-label" style="background:#aad596;">ON</div>
        <div ng-if="!value" class="label label-default switchery-label" style="background:#ccc;">OFF</div>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>