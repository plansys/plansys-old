    
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
        <!-- /data -->

        <!-- field -->
        <?php if ($this->fieldTemplate == "default"): ?>
            <div ng-repeat="item in value track by $index" class="list-view-item" style='margin-bottom:-1px;'>
                <div style="float:right;margin-top:7px;">
                    <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <div class='list-view-item-container'>
                    <input class="list-view-item-text form-control" 
                           ng-change="updateListView()"
                           ng-delay="500"
                           ng-model="value[$index]" type="text" />
                </div>
            </div>
        <?php elseif ($this->fieldTemplate == "form"): ?>
            <div ng-repeat="item in value track by $index" class="list-view-item">
                <div style="float:right;">
                    <div ng-click="removeItem($index)" class="list-view-item-remove btn btn-xs">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <div class='list-view-item-container'>
                    <?= $this->renderTemplateForm; ?>
                </div>
            </div>
        <?php endif; ?>

        <button type="button" ng-click="addItem($event)" class="btn list-view-add btn-default btn-sm">
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