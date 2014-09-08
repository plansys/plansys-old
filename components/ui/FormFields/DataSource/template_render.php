<div ps-data-source name="<?= $this->renderName ?>">

    <data name="data" class="hide"><?= json_encode($this->data['data']); ?></data>
    <data name="params" class="hide"><?= json_encode($this->params); ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>

    <?php if ($this->debugSql == 'Yes'): ?>
        <data name="debug" class="hide"><?= json_encode($this->data['debug']);  ?></data>
        <pre>{{debug | json}}</pre>
    <?php endif; ?>
</div>