<div ps-data-table>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>
    <div class="data-table-container {{ gridOptions.noReadOnlyCSS ? 'no-read-only' : '' }}">
        <div id="<?= $this->renderID ?>" class="dataTable"  style="width:100%;overflow:auto;padding-bottom:35px;"></div>
    </div>
</div>