    
<div list-view <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?> <?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
        </label>
    <?php endif; ?>
    <!-- /label -->
    
    <div ng-controller="ListViewController" class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="value" class="hide"><?= json_encode($this->value) ?></data>
        <data name="field_template" class="hide"><?= $this->fieldTemplate ?></data>
        <data name="template_attr" class="hide"><?= json_encode($this->templateAttributes) ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="datasource" class="hide"><?= $this->datasource ?></data>
        <data name="render_id" class="hide"><?= $this->renderID; ?></data>
        <data name="min_item" class="hide"><?= $this->minItem; ?></data>
        <data name="deletable" class="hide"><?= $this->deletable; ?></data>
        <data name="options" class="hide"><?= json_encode($this->options) ?></data>
        <!-- /data -->
        <!-- field -->
        <?php if ($this->insertable == 'Yes'): ?>
        <button type="button" ng-if="value.length > 5 && isInsertable" ng-click="addItem($event)" 
                style="margin:0px 0px 5px 0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
        </button>
        <?php endif; ?>
        <button type="button" ng-click="undo()" ng-if="value.length > 5 && showUndoDelete"
                style="margin:0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-undo"></i> <b>Undo Delete</b>
        </button>
        <div ng-if="!loading && value != null"
             class="list-view-form"
             oc-lazy-load="{name: 'ui.tree', files: ['<?= Yii::app()->controller->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
           
            <div ui-tree="uiTreeOptions">
                <ol ui-tree-nodes ng-model="value">
                    <li <?= $this->expandAttributes($this->fieldOptions) ?>>
                        <div style="float:right;" ng-if="!isDeleteDisabled($index)">
                            <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                                <i class="fa fa-times fa-nm"></i>
                            </div>
                        </div>

                        <div ui-tree-handle class="list-view-item-move " 
                             style="float:left;<?php if ($this->sortable == 'No'): ?>display:none !important;<?php endif ?>">
                            <i class="fa fa-arrows"></i>
                        </div>

                        <div ng-class="{'disable-delete':isDeleteDisabled($index)}" 
                            class='list-view-item-container <?php if ($this->sortable == 'No'): ?>unsorted<?php endif ?>'>
                            <?= $this->renderTemplateForm; ?>
                            <div class="clearfix"></div>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
        <div ng-show="loading || loaded" class="list-view-loading">
            <i class="fa fa-link"></i>
            Loading ListView...
        </div> 
        
        <div ng-repeat="(key,val) in value track by $index">
            <input name="<?= $this->renderName ?>[{{key}}]" ng-if="typeof (val) == 'string'" type="hidden" value='{{val}}' />
            <div ng-repeat="(k,v) in val  track by $index" ng-if="typeof (val) == 'object'">
                <input name="<?= $this->renderName ?>[{{key}}][{{k}}]" type="hidden" value='{{v}}' />
            </div>
        </div>
        <input ng-if="value.length == 0" name="<?= $this->renderName ?>" type="hidden" value='' />
        
        <?php if ($this->insertable == 'Yes'): ?>
        <button type="button" ng-if="isInsertable" ng-click="addItem($event)" 
                style="margin:0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
        </button>
        <?php endif; ?>
        <button type="button" ng-click="undo()" ng-if="showUndoDelete"
                style="margin:0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-undo"></i> <b>Undo Delete</b>
        </button>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
    <script type="text/javascript">
                app.controller("ListViewController", function ($scope, $parse, $timeout, $http, $localStorage) {
                $timeout(function () {
<?= $inlineJS ?>
                });
                });
                registerController("ListViewController");
    </script>
</div>