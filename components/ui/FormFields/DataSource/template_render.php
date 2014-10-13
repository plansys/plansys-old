<div ps-data-source name="<?= $this->renderName ?>">

    <data name="data" class="hide"><?= json_encode($this->data['data']); ?></data>
    <data name="total_item" class="hide"><?= $this->data['count']; ?></data>
    <data name="params" class="hide"><?= json_encode($this->params); ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="relation_to" class="hide"><?= $this->relationTo; ?></data>
    <data name="relation_def" class="hide"><?= json_encode($this->model->relations()); ?></data>
    <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>
    <data name="params_get" class="hide"><?= json_encode($_GET); ?></data>
    <data name="params_default" class="hide"><?= @json_encode($this->data['params']); ?></data>
    <data name="delete_data" class="hide"><?= @json_encode(@$this->data['rel']['delete_data']); ?></data>
    <?php if ($this->postData == 'Yes'): ?>
        <input name="<?= $this->getPostName('Insert'); ?>" type="hidden" value="{{ insertData | json }}" />
        <input name="<?= $this->getPostName('Update'); ?>" type="hidden" value="{{ updateData | json }}" />
        <input name="<?= $this->getPostName('Delete'); ?>" type="hidden" value="{{ deleteData | json }}" />
    <?php endif; ?>

    <?php if ($this->debugSql == 'Yes'): ?>
        <data name="debug" class="hide"><?= json_encode($this->data['debug']); ?></data>
        <pre ng-bind-html="debugHTML"></pre>
    <?php endif; ?>
</div>