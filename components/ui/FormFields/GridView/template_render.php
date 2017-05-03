<div grid-view <?= $this->expandAttributes($this->options); ?>>
    <?php
    $columnsWithoutHTML = $this->columns;
    foreach ($columnsWithoutHTML as $k => $c) {
        if (isset($columnsWithoutHTML[$k]['html'])) {
            unset($columnsWithoutHTML[$k]['html']);
        }
        if (isset($columnsWithoutHTML[$k]['labelHtml'])) {
            unset($columnsWithoutHTML[$k]['labelHtml']);
        }
    } ?>
    
    <!-- row state template -->
    <script type="text/ng-template" id="row-state-template">
        <div ng-if='!!row.$rowState' class="row-state-detail row-state-{{ row.$rowState }}">
            <i class="fa fa-pencil" ng-if="row.$rowState == 'edit'"></i>
            <i class="fa fa-plus" ng-if="row.$rowState == 'insert'"></i>
            <i class="fa fa-trash" ng-if="row.$rowState == 'remove'"></i>

            <div class="row-state-options">
                <div class="btn btn-xs btn-default" ng-click="rowUndoState(row)"> Cancel {{ row.$rowState }}</div>
            </div>
        </div>
    </script>
    
    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <data name="columns" class="hide"><?= json_encode($columnsWithoutHTML); ?></data>
    <data name="dpz" class="hide"><?= ActiveRecord::DEFAULT_PAGE_SIZE; ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>
    <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>
    <data name="columnsfp" class="hide"><?= json_encode($this->columnsFuncParams) ?></data>
    <!-- /info -->

    <div ng-if="!loaded" class="list-view-loading">
        <i class="fa fa-refresh fa-spin"></i>
        Loading Data... {{ loaded | json }}
    </div>
    
    <div ng-if="isCbFreezed" class="control-bar-spacer"></div>
    
    <div class="data-grid-paging" 
         ng-class="{freeze:isCbFreezed}" 
         ng-if="loaded && gridOptions.controlBar">
        <div class="data-grid-pagination" ng-init="checkMode()">
            <div class="pull-left" style="margin:5px;" ng-if="mode == 'full' && gridOptions.enablePaging != 'false'">Page:</div>
            <div ng-if="gridOptions.enablePaging != 'false'" class="pull-left data-grid-page-selector">
                <div class="input-group input-group-sm pull-left" style="display:block;">
                    <div class="input-group-btn pull-left" style="width:24px;">
                        <button class="btn btn-default" ng-click="firstPage();" type="button">
                            <i class="fa fa-fast-backward"></i>
                        </button>
                    </div>
                    <div class="input-group-btn pull-left" style="width:24px;">
                        <button class="btn btn-default" ng-click="prevPage();" style="width:25px;" type="button">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                    </div>
                    <input type="text"
                           ng-keyUp="pagingKeyPress($event)"
                           ng-keyPress="pagingKeyPress($event)"
                           class="text-center paging-input form-control pull-left"
                           ng-model="gridOptions.pageInfo.typingPage"/>

                    <div class="input-group-btn pull-left"
                         ng-if="showChangePage"
                         style="width:35px;margin-left:-1px;margin-right:-1px">
                        <button class="btn btn-default" 
                                ng-click="changePage();"
                                style="width:35px;font-size:7px" 
                                type="button">
                            GO
                        </button>
                    </div>

                    <div class="input-group-btn pull-left" style="width:25px;margin-left:-1px">
                        <button class="btn btn-default" ng-click="nextPage();" style="width:25px;" type="button">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="input-group-btn pull-left" style="width:25px;">
                        <button class="btn btn-default" ng-click="lastPage();" type="button">
                            <i class="fa fa-fast-forward"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div ng-if="mode=='full' && gridOptions.enablePaging != 'false'" class="pull-left" style="margin:5px">
                of {{ totalPage() | number }}
            </div>
            <div class="pull-left paging-total-record" ng-if="gridOptions.enablePaging != 'false'">
                 <div ng-if="datasource.loading || loading">
                    <i class="fa fa-refresh fa-spin"></i> Loading Data...
                </div>
                <div ng-if="!datasource.loading && !loading">
                    {{ datasource.totalItems | number }} Record{{ datasource.totalItems >1 ? 's' :'' }}
                </div>
            </div>
            <div ng-if="gridOptions.enablePaging == 'false'" 
                 style="margin:4px 0px;"
                 class="label label-default pull-left">
                 <div ng-if="datasource.loading || loading">
                    <i class="fa fa-refresh fa-spin"></i> Loading Data...
                </div>
                <div ng-if="!datasource.loading && !loading">
                    {{ datasource.totalItems | number }} Record{{ datasource.totalItems >1 ? 's' :'' }}
                </div>
            </div>
        </div>
        <div class="data-grid-pageinfo pull-right">
            <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;">
                <button ng-click="datasource.query()"
                        type="button" class="btn btn-default">
                    <i class="fa fa-refresh {{datasource.loading ? 'fa-spin' : ''}}"></i> Refresh
                </button>
                <button ng-click="reset()" ng-if="!!resetPageSetting"
                        tooltip="Reset Grid" tooltip-placement="bottom"
                        type="button" class="btn btn-default">
                    <i class="fa fa-flash"></i>
                </button>
            </div>

            <div ng-if="gridOptions.enablePaging != 'false'" class="btn-group pull-right" style="padding-top:2px;margin-left:5px;" dropdown>
                <button type="button" class="btn btn-default dropdown-toggle">
                    <span class="caret pull-right" style="margin:7px 0px 0px 5px;"></span>
                    {{gridOptions.pageInfo.pageSize}} rows / page
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li class="dropdown-toggle"
                        ng-click="gridOptions.pageInfo.pageSize = page"
                        ng-repeat="page in gridOptions.pageInfo.pageSizes">
                        <a href="#">{{page}} rows</a>
                    </li>
                </ul>
            </div>
            
            <div class="btn-group pull-right" style="padding-top:2px;">
                <button type="button" class="btn btn-default" 
                        ng-click="downloadExcel()"
                        tooltip="Download Excel" tooltip-placement="left">
                    <i class="fa fa-download"></i>
                </button>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div ng-include="templateUrl" onload="onGridRender('templateload')"></div>
</div>
