<div ps-data-grid class="data-grid">

    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>

    <div ng-if="loaded">
        <script type="text/ng-template" id="category_header"><?php include('category_header.php'); ?></script>
        <div class="data-grid-table" category-header="gridOptions"></div>
        <div class="data-grid-table" ng-grid="gridOptions"></div>
    </div>
</div>