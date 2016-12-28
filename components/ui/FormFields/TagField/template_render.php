<div tag-field <?= $this->expandAttributes($this->options); ?>>
    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="value" class="hide"><?= $this->value ?></data>
    <data name="render_id" class="hide"><?= $this->renderID ?></data>
    <data name="dropdown" class="hide"><?= $this->dropdown ?></data>
    <data name="must_choose" class="hide"><?= $this->mustChoose ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="rel_model_class" class="hide"><?= $this->modelClass ?></data>
    <data name="params" class="hide"><?= json_encode($this->params) ?></data>
    <data name="list" class="hide"><?= json_encode($this->drList) ?></data>
    <!-- /info -->

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?> <span ng-if="loading">Loading...</span>
        </label>
    <?php endif; ?>
    <!-- /label -->
    <!-- field -->
    <div class="<?= $this->fieldColClass ?>">
        <div ng-if="!tfLoaded" style="font-size:11px;color:#999;text-align:center;line-height:20px;">
            <span style="border:1px solid #ddd;border-radius:3px;padding:2px 3px;"><i class="fa fa-link"></i> Loading Tags...</span>
        </div>
        <div class="ft-field" style="{{ tfLoaded ? '' : 'opacity:0;' }}">
            <input id='<?= $this->renderID ?>' ng-value="value" />
        </div>
    </div>
    <!-- /field -->

    <input ng-repeat="v in valueArray" type="hidden" name="<?= $this->renderName; ?>[]" value="{{v}}">

    <!-- error -->
    <div ng-if="errors[name]" class="alert error alert-danger">
        {{ errors[name][0]}}
    </div>
    <!-- /error -->
</div>
