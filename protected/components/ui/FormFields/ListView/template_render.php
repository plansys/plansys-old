    
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
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <!-- /data -->

        <!-- field -->
        <?php if ($this->fieldTemplate == "default"): ?>
            <div ng-repeat="item in value track by $index" class="list-view-item">
                <input class="list-view-item-text form-control" 
                       ng-change="update()"
                       ng-delay="500"
                       ng-model="value[$index]" type="text" />
                <div ng-click="removeItem($index)" class="list-view-item-remove input-group-addon btn">
                    <i class="fa fa-times"></i>
                </div>
            </div>
        <?php endif; ?>

        <div ng-click="addItem()" class="btn list-view-add btn-default btn-sm">
            <i class="fa fa-nm fa-plus"></i> <b>Add</b>
        </div>
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