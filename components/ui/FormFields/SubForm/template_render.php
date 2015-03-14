<div sub-form <?= $this->expandAttributes($this->options) ?>>
    <!-- data -->
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="value" class="hide"><?= json_encode($this->value) ?></data>
    <data name="mode" class="hide"><?= $this->mode ?></data>
    <data name="template_attr" class="hide"><?= json_encode($this->templateAttributes) ?></data>
    <data name="model_class" class="hide"><?= @get_class($model) ?></data>
    <data name="options" class="hide"><?= json_encode($this->options) ?></data>
    <!-- /data -->
    <?= $this->renderHtml(); ?>
    <?= $this->renderInternalScript(); ?>
</div>