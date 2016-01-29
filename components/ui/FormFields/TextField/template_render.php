<div text-field <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?> <?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
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
        <data name="ng_disabled" class="hide"><?= $this->isDisabled(); ?></data>
        <!-- /data -->

        <!-- field -->
        <?php if ($this->prefix != "" || $this->postfix != ""): ?>
            <div class="input-group">
                <!-- prefix -->
                <?php if ($this->prefix != ""): ?>
                    <span class="input-group-addon"
                    style="{{ textFieldDisabled ? 'background:#fff;border:1px solid #ececeb;border-right:0px' : '' }}">
                    <?= $this->prefix ?>
                    </span>
                <?php endif; ?>

                <!-- value -->
                <input type="<?= $this->fieldType ?>" <?= $this->expandAttributes($this->fieldOptions) ?>
                       ng-model="value" ng-change="update()" value="<?= $this->value ?>"
                       />

                <!-- postfix -->
                <?php if ($this->postfix != ""): ?>
                    <span class="input-group-addon"
                    style="{{ textFieldDisabled ? 'background:#fff;border:1px solid #ececeb;border-left:0px' : '' }}">>
                    <?= $this->postfix ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- value -->
            <input type="<?= $this->fieldType ?>" <?= $this->expandAttributes($this->fieldOptions) ?>
                   <?php if ($this->autocomplete != ''): ?>autocomplete="off"<?php endif; ?>
                   ng-model="value" ng-focus="tfFocus()" ng-blur="tfBlur()" ng-change="update()" value="<?= $this->value ?>"/>

        <?php endif; ?>
        <!-- /field -->

        <div ng-if="list.length > 0" class="col-sm-12 tf-list" ng-class="{open: showDropDown}">
            <ul style="max-height:150px;overflow-y:auto;width:100%;" class="dropdown-menu">
                <li class="{{ choice.value == value ? 'hover' : ''}}" ng-click="choose(choice.value)" 
                    ng-repeat="choice in list" style="font-size:13px;">
                    <a href="">{{ autocomplete == 'php' ? choice : choice.label}}</a>
                </li>
            </ul>
        </div>

        <!-- error -->
        <div ng-if="errors[name]" class="alert error alert-danger">
            {{ errors[name][0]}}
        </div>
        <!-- /error -->
    </div>
</div>