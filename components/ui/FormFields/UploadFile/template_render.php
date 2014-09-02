<div upload-file <?= $this->expandAttributes($this->options) ?>>

    <!-- data -->
    <data name="path" class="hide"><?= $this->getUploadPath(); ?></data>
    <data name="file_type" class="hide"><?= $this->getFileType(); ?></data>
    <data name="repo_path" class="hide"><?= Setting::get('repo.path'); ?></data>
    <data name="file_check" class="hide"><?= $this->value; ?></data>
    <data name="file_desc" class="hide"></data>
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
             ng-if="file !== null || loading">
            <div ng-if="loading">
                <div ng-hide="progress >= 0 && progress <= 100">
                    <i class="fa fa-refresh fa-spin" style="margin-right:6px;"></i><b>Loading...</b>
                </div>
                <div class="progress" ng-show="progress >= 0 && progress <= 100" 
                      style="margin:0px auto -2px auto;width:100%">
                    <div class="progress-bar" 
                         role="progressbar" style="width:{{progress}}%;">
                        Uploading {{progress}}%
                    </div>
                </div>
            </div>
            <div style="margin:0px -5px;">
                <textarea name="file_desc" ng-if="!loading && file !== null" ng-model="json" 
                          style="min-width:100%;max-width:100%;font-size:12px;"
                          placeholder="File Description"
                          ng-change="saveDesc(json)">
                </textarea>
            </div>
            <div ng-if="!loading && file !== null">
                <a  href="{{ Yii.app.createUrl('/formfield/UploadFile.download', {
                        f: encode(file.path),
                        n: file.name
                        }
                    )}}" class="btn btn-xs btn-success pull-right">
                    Download
                </a>
                <div class="btn btn-xs btn-danger pull-right" style="margin-right:5px" ng-click="remove(file.path)">
                    Remove
                </div>
                <i class="fa fa-nm fa-file-{{file.type}}-o pull-left" 
                   style="margin-right:5px;margin-top:3px;"></i>
                <div style="word-wrap: break-word;width:50%;float:left;">
                    {{file.name}}
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- error -->
        <div class="alert error alert-danger" ng-show="errors.length > 0">
            <li ng-repeat="error in errors">
                {{error}}
            </li>
        </div>
        <!-- /error -->
    </div>
</div>