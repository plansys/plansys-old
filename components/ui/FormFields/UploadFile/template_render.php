<div upload-file <?= $this->expandAttributes($this->options) ?>>

    <!-- data -->
    <data name="file_type" class="hide"><?= $this->getFileType(); ?></data>
    <data name="file_dir" class="hide"><?= base64_encode($this->getUploadPath()); ?></data>
    <data name="repo_path" class="hide"><?= base64_encode(Setting::get('repo.path')); ?></data>
    <data name="value" class="hide"><?= $this->value; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="mode" class="hide"><?= $this->mode; ?></data>

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
        <div  ng-if="mode == 'Upload + Download'" >
            <input type="file" <?= $this->expandAttributes($this->fieldOptions) ?> 
                   ng-file-select="onFileSelect($files)" onclick="this.value = null"/>
            <input type="hidden"
                   id="<?= $this->renderID ?>"
                   name="<?= $this->renderName ?>" 
                   ng-value="filePath"
                   />
            <div class="form-control" 
                 style="padding:5px 10px 8px 10px;
                 margin-top: -2px;
                 height: auto;
                 border-top-left-radius: 0px;
                 border-top-right-radius: 0px;
                 height:auto;" 
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
                    <div class="file-desc-loading label label-success"
                         ng-if="fileDescLoadText"
                         style="position:absolute; 
                         font-weight:normal;
                         right:24px; 
                         font-size:11px;
                         margin-top:3px; 
                         padding:2px 3px;
                         opacity:.5;
                         border-radius:0px;
                         text-align:right;">
                        {{fileDescLoadText}}
                    </div>
                    <textarea auto-grow ng-if="!loading && file !== null" ng-model="json" 
                              style="min-width:100%;max-width:100%;font-size:12px;"
                              placeholder="File Description"
                              ng-change="saveDesc(json)" ng-delay="300">
                    </textarea>
                </div>
                <div ng-if="!loading && file !== null">
                    <a  href="{{ Yii.app.createUrl('/formfield/UploadFile.download', {
                            f: encode(file.path),
                            n: file.name
                        })}}" class="btn btn-xs btn-success pull-right">
                        Download
                    </a>
                    <div class="btn btn-xs btn-danger pull-right" style="margin-right:5px" ng-click="remove(file.path)">
                        Remove
                    </div>
                    <i class="fa fa-nm fa-file-{{file.type}}-o pull-left" 
                       style="margin-right:5px;margin-top:3px;"></i>
                    <div style="word-wrap: break-word;width:50%;float:left;">
                        {{file.name | elipsisMiddle:25 }}
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div ng-if="mode == 'Download Only'" style="margin:5px 0px">
            <div ng-if="file.name">
                <i class="fa fa-nm fa-file-{{file.type}}-o pull-left" 
                   style="margin-right:5px;margin-top:3px;"></i>
                <div style="word-wrap: break-word;width:50%;float:left;">
                    {{file.name | elipsisMiddle:30}}
                </div>
                <a  href="{{ Yii.app.createUrl('/formfield/UploadFile.download', {
                        f: encode(file.path),
                        n: file.name
                    })}}" class="btn btn-xs btn-success pull-right">
                    Download
                </a>
                <div class="clearfix"></div>
                <div style="font-size:12px;color:#999;">{{ json}}</div>
            </div>
            <div ng-if="!file.name" style="font-size:12px;color:#999;">
                - EMPTY -
            </div>
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