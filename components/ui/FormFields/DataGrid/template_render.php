<div ps-data-grid class="data-grid">

    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>

    <div ng-if="loaded">
        <script type="text/ng-template" id="category_header"><?php include('category_header.php'); ?></script>

        <?php if ($this->gridOptions['enablePaging'] == 'true'): ?>
            <div class="data-grid-paging">
                <div class="data-grid-pagination">
                    <div class="pull-left" style="margin:5px">Page:</div>

                    <div class="pull-left">
                        <div class="data-grid-page-selector">
                            <div class="input-group input-group-sm">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" ng-click="grid.pageBackward()" type="button">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                </span>
                                <input type="text" class="text-center form-control"
                                       ng-keypress="pagingKeypress($event)"
                                       ng-model="gridOptions.pagingOptions.currentPage">
                                <span class="input-group-btn">
                                    <button class="btn btn-default"  ng-click="grid.pageForward()" type="button">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="pull-left" style="margin:5px">
                        Of {{ Math.ceil(datasource.totalItems / gridOptions.pagingOptions.pageSize) }} 
                    </div>
                    <div class="pull-left" 
                         style="border-left:1px solid #ccc;margin:2px 5px;padding:3px 8px;">
                        Total of {{ datasource.totalItems }} Record{{ datasource.totalItems >1 ? 's' :'' }}
                    </div>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="data-grid-table" category-header="gridOptions"></div>
        <div class="data-grid-table" ng-init="initGrid()" ng-grid="gridOptions"></div>
    </div>
</div>