<div upload-file <?= $this->expandAttributes($this->options) ?>>

    <!-- data -->
    <data name="path" class="hide"><?= $this->getUploadPath(); ?></data>
    <data name="repo_path" class="hide"><?= Setting::get('repo.path'); ?></data>
    <data name="file_update" class="hide"><?= $this->value; ?></data>
    <!-- /data -->

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">

        <input type="file" <?= $this->expandAttributes($this->fieldOptions) ?> 
               ng-file-select="onFileSelect($files)" onclick="this.value = null"/>

        <input type="hidden"
               id="<?= $this->renderID ?>"
               name="<?= $this->renderName ?>" 
               ng-value="filePath"
               />
        <div class="form-control" 
             style="padding:5px 10px 8px 10px;margin-top:5px;height:auto;" 
             ng-if="file.name != ''">
            <a  href="{{ Yii.app.createUrl('/formfield/UploadFile.download', {
                    f: encode(file.path),
                    n: file.name
                    }
                )}}" class="btn btn-xs btn-success pull-right">
                Download
            </a>
            <div class="btn btn-xs btn-danger pull-right" style="margin-right:5px" ng-click="remove()">
                Remove
            </div>
            <i class="fa fa-file-{{file.type}}-o pull-left" 
               style="margin-right:5px;margin-top:3px;"></i>
            <div style="word-wrap: break-word;width:50%;float:left;">
                {{file.name}}
            </div>
            <div class="clearfix"></div>

        </div>
        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>