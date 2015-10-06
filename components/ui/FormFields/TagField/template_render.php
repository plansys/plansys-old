<div tag-field <?= $this->expandAttributes($this->options); ?>>
    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="value" class="hide"><?= $this->value ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <!-- /info -->

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->
    
    <!-- field -->
    <div class="<?= $this->fieldColClass ?>">
        EXAMPLE FIELD
        
    </div>
    <!-- /field -->
    
    <!-- error -->
    <div ng-if="errors[name]" class="alert error alert-danger">
        {{ errors[name][0]}}
    </div>
    <!-- /error -->
</div>
