<div ps-data-source name="<?= $this->renderName ?>">
    <data name="data" class="hide"><?= htmlentities(json_encode($this->data['data'], JSON_PARTIAL_OUTPUT_ON_ERROR)); ?></data>
    <data name="total_item" class="hide"><?= $this->data['count']; ?></data>
    <data name="params" class="hide"><?= json_encode($this->params); ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="relation_to" class="hide"><?= $this->relationTo; ?></data>
    <data name="relation_def"
          class="hide"><?= json_encode(method_exists($this->model, 'relations') ? $this->model->relations() : []); ?></data>
    <data name="post_data" class="hide"><?= $this->postData; ?></data>
    <data name="primary_key" class="hide"><?= $this->getPrimaryKey(); ?></data>
    <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>
    <data name="params_get" class="hide"><?= json_encode($_GET); ?></data>
    <data name="params_default" class="hide"><?= @json_encode($this->data['params']); ?></data>
    <data name="delete_data" class="hide"><?= @json_encode(@$this->data['rel']['delete_data']); ?></data>

    <?php if ($this->postData == 'Yes'): ?>
        <input name="<?= $this->getPostName('Insert'); ?>" type="hidden" value="{{ insertData | json }}"/>
        <input name="<?= $this->getPostName('Update'); ?>" type="hidden" value="{{ updateData | json }}"/>
        <input name="<?= $this->getPostName('Delete'); ?>" type="hidden" value="{{ deleteData | json }}"/>
    <?php endif; ?>

    <?php if ($this->debugSql == 'Yes'): ?>
        <pre style="margin:20px 0px -7px 0px;font-weight:bold;"><i class="fa fa-database"></i> <?php echo $this->name; ?> Debug:</pre>
        <?php if (isset($this->data['debug']['sql'])): ?>
            <div style="margin-bottom:-16px">
                <?php
                echo $this->data['debug']['sql'];
                unset($this->data['debug']['sql']);
                ?>
            </div>
        <?php endif; ?>
        <data name="debug" class="hide"><?= json_encode($this->data['debug']); ?></data>
        <pre ng-bind-html="debugHTML"></pre>
    <?php endif; ?>
    <div class="error" style="display:none;">
        <div style="position:absolute;color:red;top:0px;padding:10px;
             font-size:24px;left:0px;right:0px;height:50px;
             text-align:center;z-index:999;
             background:#fff;border-bottom:1px solid #ddd;">
            <i class="fa fa-warning"></i> <?= $this->name; ?> Debug Info:
        </div>
        <iframe style="position:absolute;top:50px;left:0px;bottom:0px;
                right:0px;width:100%;height:100%;
                border:0px;z-index:999;"></iframe>

    </div>
</div>