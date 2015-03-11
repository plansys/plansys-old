    
<div list-view  <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderName; ?>">
                <?= $this->label ?>
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
        <data name="options" class="hide"><?= json_encode($this->options) ?></data>
        <!-- /data -->
        <!-- field -->
        <button type="button" ng-if="value.length > 5" ng-click="addItem($event)" 
                style="margin:0px 0px 5px 0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
        </button>
        <?php if ($this->fieldTemplate == "default"): ?>
            <div ng-if="value != null" oc-lazy-load="{name: 'ui.tree', files: ['<?= Yii::app()->controller->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree="uiTreeOptions">
                    <ol ui-tree-nodes ng-model="value">
                        <li ui-tree-node ng-repeat="(key, item) in value track by $index" class="list-view-item">
                            <div style="float:right;margin-top:7px;">
                                <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                            <div ui-tree-handle class="list-view-item-move " style="display:none;float:left;padding-top:7px;">
                                <i class="fa fa-arrows"></i>
                            </div>
                            <div class='list-view-item-container'>
                                <input class="list-view-item-text form-control"
                                       ng-model="item"
                                       ng-change="value[$index] = item"
                                       type="text" />
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        <?php elseif ($this->fieldTemplate == "form"): ?>
            <div ng-if="!loading && value != null"
                 class="list-view-form"
                 oc-lazy-load="{name: 'ui.tree', files: ['<?= Yii::app()->controller->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree="uiTreeOptions">
                    <ol ui-tree-nodes ng-model="value">
                        <li ui-tree-node ng-init="model = value[$index]; model.$parent = parent.model;" ng-repeat="item in value" class="list-view-item">
                            <div style="float:right;">
                                <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                            <div ui-tree-handle class="list-view-item-move " style="float:left;">
                                <i class="fa fa-arrows"></i>
                            </div>
                            <div class='list-view-item-container'>
                                <?= $this->renderTemplateForm; ?>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <div ng-show="loading" class="list-view-loading">
                <i class="fa fa-link"></i>
                Loading ListView...
            </div>
        <?php endif; ?>

        <div ng-repeat="(key,val) in value track by $index">
            <input name="<?= $this->renderName ?>[{{key}}]" ng-if="typeof (val) == 'string'" type="hidden" value='{{val}}' />
            <div ng-repeat="(k,v) in val  track by $index" ng-if="typeof (val) == 'object'">
                <input name="[{{key}}][{{k}}]" type="hidden" value='{{v}}' />
            </div>
        </div>
        <input ng-if="value.length == 0" name="<?= $this->renderName ?>" type="hidden" value='' />

        <button type="button" ng-click="addItem($event)" 
                style="margin:0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
        </button>
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