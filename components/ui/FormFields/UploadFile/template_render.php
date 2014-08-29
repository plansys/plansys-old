<div upload-file <?= $this->expandAttributes($this->options) ?>>
     <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->
    
    <div class="<?= $this->fieldColClass ?>">
        
        <input type="file" <?= $this->expandAttributes($this->fieldOptions) ?> />
        
        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>