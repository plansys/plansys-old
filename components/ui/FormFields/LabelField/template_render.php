    
<div label-field <?= $this->expandAttributes($this->options) ?>>

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
        <data name="js" class="hide"><?= $this->js ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <!-- /data -->

        <!-- field -->
        <div <?= $this->expandAttributes($this->fieldOptions) ?>>
            {{ value }} &nbsp;
        </div>
        <input type="hidden"
               id='<?= $this->renderID ?>'
               name='<?= $this->renderName ?>'
               ng-model="value"
               ng-value="value"
               />
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