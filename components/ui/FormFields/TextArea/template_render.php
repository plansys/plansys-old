<div text-area <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <!-- /data -->

        <!-- field -->
        <textarea <?= $this->expandAttributes($this->fieldOptions) ?>
            ng-model="value" ng-change="update()"><?= $this->value ?></textarea>
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