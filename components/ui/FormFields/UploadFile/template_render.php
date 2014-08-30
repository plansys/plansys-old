<div upload-file <?= $this->expandAttributes($this->options) ?>>

    <!-- data -->
    <data name="path" class="hide"><?= $this->getUploadPath(); ?></data>
    <data name="repo_path" class="hide"><?= Setting::get('repo.path'); ?></data>
    <!-- /data -->

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->
    
    <?= $this->value; ?>
    <div class="<?= $this->fieldColClass ?>">

        <input type="file" <?= $this->expandAttributes($this->fieldOptions) ?> 
               ng-file-select="onFileSelect($files)" onclick="this.value = null"/>

        <input type="hidden"
               id="<?= $this->renderID ?>"
               name="<?= $this->renderName ?>" 
               ng-value="file"
        />
        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>