<div ps-data-table>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>
    <div ng-class="{invisible: !loaded}"   
         class=" data-table-container {{ gridOptions.noReadOnlyCSS ? 'no-read-only' : '' }}">
        <div ng-class="{invisible: datasource.data.length == 0}"  
             id="<?= $this->renderID ?>" class="dataTable" 
             style="
             width:100%;
             overflow:auto;
             padding-bottom:35px;"></div>

        <div ng-if="loaded && datasource.data.length == 0"
             style="text-align:center;padding:20px;color:#ccc;font-size:25px;">
            &mdash; {{ !datasource.loading ? 'Data Empty' : 'Loading Data'; }} &mdash;
        </div>

    </div>
    <div style="margin-top:15px;" ng-if="!loaded">    
        <div class="list-view-loading">
            <i class="fa fa-link"></i>
            Loading DataTable...
        </div>
    </div>
</div>