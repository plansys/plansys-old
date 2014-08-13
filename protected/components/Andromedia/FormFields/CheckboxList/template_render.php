<div check-box-list convertToString="<?= $this->convertToString ?>"
     <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" style="padding-top:5px;">
        <!-- data -->
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="selected" class="hide"><?= json_encode($this->value); ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list); ?></data>
        <!-- /data -->

        <!-- field -->
        <span ng-repeat="(value, text) in formList track by $index">
            <label <?= $this->expandAttributes($this->fieldOptions) ?>>
                <input type="checkbox" id="<?= $this->renderID ?>_{{value}}"
                       name="<?= $this->name ?>[{{value}}]"
                       ng-checked="selected.indexOf(value) > -1"
                       ng-click="updateItem(value)"
                       /> {{ text}}
            </label>
        </span>

        <?php if ($this->convertToString == "Yes"): ?>
            <input type="text" class="invisible"
                   ng-model="selectedText" id="<?= $this->renderID ?>"
                   name="<?= $this->name ?>" value='<?= $this->value ?>'/>
               <?php endif; ?>
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