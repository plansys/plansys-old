<div icon-picker <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    
    <div class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <!-- /data -->

        <!-- field -->
        <div ng-click="open()" class="btn-popover btn btn-sm btn-default pull-left">
            <span class="popover-icon" ng-bind-html="icon">
                <?= $this->icon ?>
            </span>
            <span class="popover-text" ng-bind-html="value">
                <?= $this->value ?>
            </span>
        </div>

        <div class="popover" ng-class="{show: is_open}">
            <div class="popover-header" style="font-size:11px;padding:3px;border-bottom:1px solid #ececeb;">
                <div ng-click="is_open = false" class="btn-popover-close btn btn-xs btn-default"
                     style="padding:0px 3px;line-height:14px;font-size:11px;position:absolute;top:4px;right:4px;">
                    <i class="fa fa-times"></i> Close
                </div>
                <input class="popover-search"
                       ng-model="search"
                       placeholder="&#xF002; Search ..."
                       style="font-size:11px;border:0px;width:100%;outline:0px;font-family:Arial, FontAwesome;"
                       type="text" value="" />

            </div>
            <div class="popover-content" style="width:<?= $this->fieldWidth ?>px;">
                <?php
                foreach ($this->list as $value => $text):
                    ?>
                    <div ng-show="'<?= $value ?>'.indexOf(search) > -1 || search.trim() == ''"
                         ng-click="select('<?= $value ?>')"
                         ng-class="iconClass('<?= $value ?>')"
                         class="btn btn-popover-item btn-xs"
                         value="<?= $value ?>" style="margin-bottom:4px;">
                             <?= $this->getIcon($value) ?>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
        </div>
        <input type="text" class="invisible"
               ng-model="value" id="<?= $this->renderID ?>"
               name="<?= $this->renderName ?>" value='<?= $this->value ?>'/>
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