<div list-view <?= $this->expandAttributes($this->options) ?>>
    <div class="<?= $this->fieldColClass ?>" style="padding-top:5px;">
        <!-- data -->
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="selected" class="hide"><?= json_encode($this->selected); ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list); ?></data>
        <!-- /data -->
        <!-- layout -->
        <div class="list-view-layout" style="display:none;"><?= $this->layout ?></div>
        <!-- layout -->

        <div>
            <?= $this->header ?>
        </div> 
        <!-- field -->
        <div ng-repeat="item in formList" ng-include="'layout'"></div>
        <!-- /field -->
        <div>
            <?= $this->footer ?>
        </div> 
    </div>
</div>