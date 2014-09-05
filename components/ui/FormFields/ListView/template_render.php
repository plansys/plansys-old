    
<div list-view <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderName; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="value" class="hide"><?= json_encode($this->value) ?></data>
        <data name="field_template" class="hide"><?= $this->fieldTemplate ?></data>
        <data name="template_attr" class="hide"><?= json_encode($this->templateAttributes) ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="options" class="hide"><?= json_encode($this->options) ?></data>
        <!-- /data -->

        <!-- field -->
        <?php if ($this->fieldTemplate == "default"): ?>
            <div ng-if="value != null" ui-tree="uiTreeOptions">
                <ol ui-tree-nodes ng-model="value">
                    <li ui-tree-node ng-repeat="item in value track by $index" class="list-view-item">
                        <div style="float:right;margin-top:7px;">
                            <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                                <i class="fa fa-times"></i>
                            </div>
                        </div>
                        <div ui-tree-handle class="list-view-item-move " style="float:left;padding-top:7px;">
                            <i class="fa fa-arrows"></i>
                        </div>
                        <div class='list-view-item-container'>
                            <input class="list-view-item-text form-control" 
                                   ng-change="updateListView()"
                                   ng-delay="500"
                                   ng-model="value[$index]" type="text" />
                        </div>
                    </li>
                </ol>
            </div>
        <?php elseif ($this->fieldTemplate == "form"): ?>
            <div ng-if="!loading && value != null" class="list-view-form" ui-tree="uiTreeOptions">
                <ol ui-tree-nodes ng-model="value">
                    <li ui-tree-node ng-repeat="item in value track by $index" class="list-view-item">
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
                        </div>
                    </li>
                </ol>
            </div>
            <div ng-show="loading" class="list-view-loading">
                <i class="fa fa-link"></i>
                Loading <?= @get_class($model) ?>...
            </div>
        <?php endif; ?>

        <button type="button" ng-click="addItem($event)" 
                style="margin:0px;"
                class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
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
</div>