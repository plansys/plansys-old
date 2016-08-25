<div oc-lazy-load="{name: 'angularFileUpload', files: [
     '<?= Yii::app()->controller->staticUrl('/js/lib/afu/angular-file-upload-shim.min.js') ?>'
     , '<?= Yii::app()->controller->staticUrl('/js/lib/afu/angular-file-upload.js') ?>' ]}">
    <div upload-file <?= $this->expandAttributes($this->options) ?>>

        <!-- data -->
        <data name="file_type" class="hide"><?= $this->getFileType(); ?></data>
        <data name="repo_path" class="hide"><?= base64_encode(Setting::get('repo.path')); ?></data>
        <data name="value" class="hide"><?= $this->value; ?></data>
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="mode" class="hide"><?= $this->mode; ?></data>
        <data name="options" class="hide"><?= json_encode($this->options); ?></data>
        <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>
        <data name="allow_delete" class="hide"><?= $this->allowDelete; ?></data>
        <data name="allow_overwrite" class="hide"><?= $this->allowOverwrite; ?></data>

        <!-- /data -->

        <!-- label -->
        <?php if ($this->label != ""): ?>
            <label <?= $this->expandAttributes($this->labelOptions) ?>
                class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                    <?= $this->label ?><?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
            </label>
        <?php endif; ?>
        <!-- /label -->


        <div class="<?= $this->fieldColClass ?>">
            <input type="hidden"
                   id="<?= $this->renderID ?>"
                   name="<?= $this->renderName ?>" 
                   ng-value="value"
                   />
            <div ng-if="file == null && mode != 'Download Only'
                            && (allowOverwrite == 'Yes' || allowOverwrite == 'No' && file === null)" >

                <div ng-if="mode == 'Upload + Browse + Download' && choosing != 'Upload'" class="form-control" style="height:auto;padding-top:0px;padding-bottom:0px;">
                    <div style="margin:3px -6px;" class="btn btn-default btn-xs" ng-click="choose('Browse')">
                        <i class="fa fa-folder-open"></i>   Browse Repository
                    </div>
                    <label for="<?= $this->renderID . "inf" ?>" style="margin:3px -6px;" 
                           class="btn btn-default pull-right btn-xs" ng-click="choose('Upload')">
                        <i class="fa fa-upload"></i> Upload File
                    </label>
                    <div class="clearfix"></div>
                </div>

                <div class="upload-field-internal" ng-if="choosing == 'Upload' || mode.indexOf('Upload') >= 0">    
                    <div ng-show="choosing == 'Upload' && mode == 'Upload + Browse + Download'"
                         class=" pull-right">
                        <div class="btn btn btn-xs btn-default"
                             ng-click="choose('')"
                             style="position:absolute;margin:6px 0px 0px -56px;color:green;">
                            Cancel
                        </div>
                    </div>

                    <input id="<?= $this->renderID . "inf" ?>"
                           ng-show="choosing == 'Upload' || mode != 'Upload + Browse + Download'" 
                           type="file" <?= $this->expandAttributes($this->fieldOptions) ?> 
                           ng-file-select="onFileSelect($files)" onclick="this.value = null"/>
                </div>
                
                <div class="form-control" 
                     style="padding:5px 5px 5px 5px;
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
                    <div class="clearfix"></div>
                </div>
            </div>

            <div ng-if="!loading && file !== null" class="form-control"
                 style="padding:5px 5px 3px 0px;height:auto;box-shadow:none; border-color:#ececeb;text-align:left;">
                <div ng-if="file.name">
                    <a  href="{{ Yii.app.createUrl('/formfield/UploadFile.download', {
                            f: file.downloadPath,
                            n: file.name
                        })}}" class="btn btn-success btn-xs" style="margin:-2px 0px 0px 4px;">
                        <i class="fa fa-download"></i>
                        Download 
                    </a>

                    <div class="btn btn-xs btn-default pull-right" 
                         style="margin-right:0px;margin-top:-2px;margin-left:5px;"
                         ng-if="allowDelete == 'Yes'" ng-click="remove(file.downloadPath)">
                        <i class="fa fa-trash fa-nm"></i>
                    </div>
                    <div class="btn btn-xs btn-default pull-right" style="margin-right:0px;margin-top:-2px;"
                         ng-if="allowOverwrite == 'Yes' && mode != 'Download Only'" ng-click="choose('')">
                         <i class="fa fa-folder-open"></i> Choose File
                    </div>
                    <div class="clearfix"></div>
                    <div style="font-size:12px;color:#999;">{{ json}}</div>
                    <div ng-if="thumb != ''" style="padding:5px;text-align:center;
                         border-top:1px solid #ddd;
                         margin:5px -5px 0px 0px;
                         padding-top:5px;">
                        <img style="max-width:100%;" ng-src="{{thumb}}" alt="" />
                    </div>
                </div>
                <div ng-if="!file.name" style="font-size:12px;color:#999;text-align:center;">
                    &mdash; EMPTY &mdash;
                </div>
            </div>

            <div ng-if="(file === null || loading) && mode == 'Download Only'" class="form-control"
                 style="padding:5px 5px 3px 0px;height:auto;box-shadow:none; border-color:#ececeb;">
                <div style="font-size:12px;color:#999;text-align:center;">
                    &mdash; {{ file == null ? 'EMPTY' : 'LOADING' }} &mdash;
                </div>
            </div>

            <!-- error -->
            <div ng-if="errors[name]" class="alert error alert-danger">
                {{ errors[name][0]}}
            </div>
            <!-- /error -->
        </div>

        <?php
        echo FormBuilder::build('RepoBrowser', [
            'name' => 'BrowseDialog',
            'showBrowseButton' => 'No',
        ]);
        ?>

    </div>
</div>