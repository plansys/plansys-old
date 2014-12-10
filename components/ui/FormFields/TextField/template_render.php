<div text-field <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">

        <!-- data -->
        <data name="name" class="hide"><?= $this->name ?></data>
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="autocomplete" class="hide"><?= $this->autocomplete ?></data>
        <data name="ac_mode" class="hide"><?= $this->acMode ?></data>
        <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
        <data name="rel_model_class" class="hide"><?= $this->modelClass ?></data>
        <data name="params" class="hide"><?= json_encode($this->params) ?></data>
        <data name="list" class="hide"><?= json_encode($this->acList) ?></data>
        <!-- /data -->

        <!-- field -->
        <?php if ($this->prefix != "" || $this->postfix != ""): ?>
            <div class="input-group">
                <!-- prefix -->
                <?php if ($this->prefix != ""): ?>
                    <span class="input-group-addon">
                        <?= $this->prefix ?>
                    </span>
                <?php endif; ?>

                <!-- value -->
                <input type="<?= $this->fieldType ?>" <?= $this->expandAttributes($this->fieldOptions) ?>
                       ng-model="value" ng-change="update()" value="<?= $this->value ?>"
                       />

                <!-- postfix -->
                <?php if ($this->postfix != ""): ?>
                    <span class="input-group-addon">
                        <?= $this->postfix ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- value -->
            <input type="<?= $this->fieldType ?>" <?= $this->expandAttributes($this->fieldOptions) ?>
                   <?php if ($this->autocomplete != ''): ?>autocomplete="off"<?php endif; ?>
                   ng-model="value" ng-change="update()" value="<?= $this->value ?>"/>

        <?php endif; ?>
        <!-- /field -->

        <span dropdown>
            <ul ng-if="list.length > 0" style="margin-left:15px;max-height:150px;overflow-y:auto;" class="dropdown-menu">
                <li class="{{ choice.value == value ? 'hover' : ''}}" ng-repeat="choice in list" style="font-size:13px;">
                    <a href=""  ng-click="choose(choice.value)">{{ autocomplete == 'php' ? choice : choice.label}}</a>
                </li>
            </ul>

            <ul ng-if="(list.length == 0)" class="dropdown-menu" style="margin-left:15px;max-height:150px;overflow-y:auto;">
                <li style="text-align:center;padding:10px;font-size:12px;color:#999;">&mdash; Not Found &mdash;</li>
            </ul>
        </span>



        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>