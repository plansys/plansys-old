<div class="data-grid-loader" name="<?= $this->name; ?>" oc-lazy-load="{name: 'ngGrid', files: [
     '<?= Yii::app()->controller->staticUrl('/js/lib/ng-grid.debug.js') ?>'
     , '<?= Yii::app()->controller->staticUrl('/css/ng-grid.css') ?>' ]}">
    <div 
        oc-lazy-load="{name: 'ngFileUpload', files: [
         '<?= Yii::app()->controller->staticUrl('/js/lib/afu/ng-file-upload-shim.min.js') ?>'
         , '<?= Yii::app()->controller->staticUrl('/js/lib/afu/ng-file-upload.js') ?>'  ]}">
        <div ps-data-grid id="<?= $this->name ?>" class="data-grid">

            <data name="datasource" class="hide"><?= $this->datasource; ?></data>
            <data name="name" class="hide"><?= $this->name; ?></data>
            <data name="render_id" class="hide"><?= $this->renderID; ?></data>
            <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
            <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
            <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>
            <div ng-if="!loaded" class="list-view-loading">
                <i class="fa fa-link"></i>
                Loading DataGrid...
            </div>
            <div ng-if="!datasource && loaded" class="list-view-loading">
                <i class="fa fa-warning"></i>
                {{name}}: Please choose Data Source in Form Builder
            </div>
            <div class="data-grid-container" ng-if="loaded">
                <script type="text/ng-template" id="category_header"><?php include('category_header.php'); ?></script>
                <div ng-if="datasource.data.length != 0 || gridOptions.enableExcelMode" class="data-grid-paging-shadow"
                     style="height:50px;display:none;"></div>
                <div class="data-grid-paging"
                     ng-if="gridOptions.enablePaging ||
                                     gridOptions.enableExcelMode ||
                                     gridOptions.enableCellEdit ||
                                     gridOptions.enableImport ||
                                     gridOptions.enableExport">

                    <div ng-if="gridOptions.enableExcelMode"
                    <?php if (@$this->gridOptions['enablePaging'] == 'true'): ?>
                             style="float:left;border-right:1px solid #ccc;padding-right:10px;margin-right:5px;"
                         <?php else: ?>
                             style="float:left;margin-left:-5px;"
                         <?php endif; ?>
                         class="data-grid-pageinfo">
                        <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;">
                            <button ng-click="addRow(excelModeSelectedRow)" type="button" class="btn btn-default">
                                <i class="fa fa-plus"></i> Add
                            </button>
                            <button ng-click="removeRow(excelModeSelectedRow)"
                                    ng-if="grid.selectedItems.length > 0"
                                    type="button" class="btn btn-default">
                                <i class="fa fa-times"></i> Remove
                            </button>
                        </div>

                        <div class="pull-right" ng-if="!gridOptions.enablePaging" style="margin:5px;">
                            Row:
                        </div>
                    </div>

                    <div ng-if="gridOptions.enableImport || gridOptions.enableExport"
                    <?php if (@$this->gridOptions['enablePaging'] == 'true'): ?>
                             style="float:right;margin:2px 0px 0px 5px;"
                         <?php elseif (@$this->gridOptions['enableExcelMode'] == 'true'): ?>
                             style="float:left;border-right:1px solid #ccc;padding-right:10px;margin-right:5px;"
                         <?php endif; ?>>

                        <div class="btn-group pull-left">
                            <div ng-if="gridOptions.enableImport" class="btn-group" dropdown>
                                <button type="button" class="btn btn-default dropdown-toggle">
                                    <i class="fa {{importIcon}}"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" ng-class="{'pull-right':gridOptions.enablePaging}"
                                    style="z-index:99;" role="menu">
                                    <li style="overflow:hidden;cursor:pointer;">
                                        <a style="padding:3px 7px;" dropdown-toggle href="#">Load From
                                            Excel</a>
                                        <input style="position:absolute;opacity:0;cursor:pointer;margin-top:-25px;"
                                               onmouseover="$(this).prev().css('background', '#f5f5f5');"
                                               onmouseout="$(this).prev().css('background', '#fff');"
                                               type="file" ngf-select="loadExcel($files)"/>
                                    </li>
                                    <li class="divider" style="margin:0px;"></li>
                                    <li><a style="padding:3px 7px;"
                                           ng-click="generateTemplate()"
                                           dropdown-toggle href="#">Download Template</a>
                                    </li>
                                </ul>
                            </div>

                            <div ng-if="gridOptions.enableExport" class="btn-group" dropdown>
                                <div ng-if="!gridOptions.enableImport
                                                && !gridOptions.enablePaging
                                                && !gridOptions.enableExcelMode" 
                                     class="pull-left" style="margin:2px 10px 0px 0px;">Export: </div>
                                <button type="button" class="btn btn-default dropdown-toggle">
                                    <i class="fa fa-download"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" ng-class="{
                                        'pull-right'
                                                : gridOptions.enablePaging}"
                                    style="z-index:99;" role="menu">
                                    <li><a style="padding:3px 7px;"
                                           ng-click="exportExcel()"
                                           dropdown-toggle href="#">
                                            <i class="fa fa-file-excel-o"></i>
                                            Export To Excel</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="data-grid-pageinfo pull-right" ng-if="gridOptions.enablePaging">
                        <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;">
                            <button ng-click="datasource.query()"
                                    type="button" class="btn btn-default">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                            <button ng-click="reset()"
                                    type="button" class="btn btn-default">
                                <i class="fa fa-repeat"></i> Reset
                            </button>
                        </div>

                        <div class="btn-group pull-right" style="padding-top:2px;" dropdown>
                            <button type="button" class="btn btn-default dropdown-toggle">

                                <span class="caret pull-right"
                                      style="margin:7px 0px 0px 5px;"></span>
                                {{gridOptions.pagingOptions.pageSize}}
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li class="dropdown-toggle"
                                    ng-click="gridOptions.pagingOptions.pageSize = page"
                                    ng-repeat="page in gridOptions.pagingOptions.pageSizes">
                                    <a href="#">{{page}}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="pull-right" style="margin:5px;">
                            View Per Page:
                        </div>
                    </div>

                    <?php if (@$this->gridOptions['badge']): ?>
                        <div style="
                             left:0px;
                             position:absolute;
                             font-size:13px;
                             text-align:center;
                             margin-top:6px;
                             width:100%;
                             pointer-events: none;
                             ">
                                 <?php $badges = explode("|||", $this->gridOptions['badge']); ?>
                                 <?php foreach ($badges as $badge): ?>
                                <div class="badge">
                                    <?php $badge = str_replace('||', '<span class="badge-separator"></span>', $badge); ?>
                                    <?= $badge ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="data-grid-pagination" ng-if="gridOptions.enablePaging">
                        <div class="pull-left" style="margin:5px;">Page:</div>
                        <div class="pull-left data-grid-page-selector">
                            <div class="input-group input-group-sm pull-left" style="display:block;">
                                <div class="input-group-btn pull-left" style="width:24px;">
                                    <button class="btn btn-default" ng-click="prevPage();" type="button">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                </div>
                                <input type="text" class="text-center paging-input form-control pull-left"
                                       ng-change="pagingKeypress($event)"
                                       ng-delay="500"
                                       ng-model="gridOptions.pagingOptions.currentPage" />
                                <div class="input-group-btn pull-left" style="width:25px;">
                                    <button class="btn btn-default" ng-click="nextPage();" type="button">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="pull-left" style="margin:5px">
                            of {{ Math.ceil(datasource.totalItems / gridOptions.pagingOptions.pageSize) | number}}
                        </div>
                        <div class="pull-left"
                             style="border-left:1px solid #ccc;margin:2px 5px;padding:3px 8px;">

                            <div ng-if="datasource.loading">
                                <i class="fa fa-refresh fa-spin"></i> Loading Data...
                            </div>
                            <div ng-if="!datasource.loading">
                                Total of {{ datasource.totalItems | number }} Record{{ datasource.totalItems >1 ? 's' :'' }}
                            </div>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div ng-if="datasource.data.length == 0 && !gridOptions.enableExcelMode"
                     style="text-align:center;padding:20px;color:#ccc;font-size:25px;">
                    &mdash; {{ !datasource.loading ? 'Data Empty' : 'Loading Data'; }} &mdash;
                </div>
                <div style="margin:0px 0px;{{ datasource.data.length != 0 || gridOptions.enableExcelMode ? '' : 'opacity:0'}}">
                    <div class="data-grid-category" category-header="gridOptions"></div>
                    <div class="data-grid-table" ng-init="initGrid()" ng-grid="gridOptions"></div>
                </div>
            </div>
        </div>
    </div>
</div>