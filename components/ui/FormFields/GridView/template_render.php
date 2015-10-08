<div grid-view <?= $this->expandAttributes($this->options); ?>>
    <?php
    $columnsWithoutHTML = $this->columns;
    foreach ($columnsWithoutHTML as $k => $c) {
        unset($columnsWithoutHTML[$k]['html']);
    } ?>

    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <data name="columns" class="hide"><?= json_encode($columnsWithoutHTML); ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>
    <!-- /info -->

    <div ng-if="!loaded" class="list-view-loading">
        <i class="fa fa-refresh fa-spin"></i>
        Loading Data...
    </div>

    <div class="data-grid-paging" ng-if="loaded && gridOptions.controlBar">
        <div class="data-grid-pagination" ng-init="checkMode()">
            <div class="pull-left" style="margin:5px;" ng-if="mode == 'full'">Page:</div>
            <div class="pull-left data-grid-page-selector">
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
                        <button class="btn btn-default" ng-click="changePage();"
                                style="width:35px;font-size:7px" type="button">
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
            <div class="pull-left" style="margin:5px">
                of {{ Math.ceil(datasource.totalItems / gridOptions.pageInfo.pageSize) | number}}
            </div>
            <div ng-if="mode=='full'"
                 style="float:left; border-left:1px solid #ccc;margin:2px 4px 2px 5px;padding:3px 0px 3px 8px;">
                {{ (gridOptions.pageInfo.pageSize * (gridOptions.pageInfo.currentPage -1)) + 1| number }}
                &nbsp;<i class="fa fa-caret-right"></i>
                {{ Math.min(gridOptions.pageInfo.pageSize * gridOptions.pageInfo.currentPage, datasource.totalItems) }}
            </div>
            <div ng-if="mode=='full'" class="pull-left"
                 style="border-left:1px solid #ccc;margin:2px 5px;padding:3px 8px;">

                <div ng-if="datasource.loading">
                    <i class="fa fa-refresh fa-spin"></i> Loading Data...
                </div>
                <div ng-if="!datasource.loading">
                    {{ datasource.totalItems | number }} Record{{ datasource.totalItems >1 ? 's' :'' }}
                </div>
            </div>
        </div>
        <div class="data-grid-pageinfo pull-right">
            <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;">
                <button ng-click="datasource.query()"
                        type="button" class="btn btn-default">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
                <button ng-click="reset()" ng-if="!!resetPageSetting"
                        type="button" class="btn btn-default">
                    <i class="fa fa-flash"></i>
                </button>
            </div>

            <div class="btn-group pull-right" style="padding-top:2px;" dropdown>
                <button type="button" class="btn btn-default dropdown-toggle">
                    <span class="caret pull-right" style="margin:7px 0px 0px 5px;"></span>
                    {{gridOptions.pageInfo.pageSize}}
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li class="dropdown-toggle"
                        ng-click="gridOptions.pageInfo.pageSize = page"
                        ng-repeat="page in gridOptions.pageInfo.pageSizes">
                        <a href="#">{{page}}</a>
                    </li>
                </ul>

            </div>

            <div ng-if="mode=='full'" class="pull-right" style="margin:5px;">
                View Per Page:
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="thead">
        <div class="tr">
            <?php foreach ($this->columns as $idx => $col) {
                echo $this->getHeaderTemplate($col, $idx);
            } ?>
        </div>
        <div style="position:absolute;right:0px;">
            <div class="btn btn-default btn-small"
                 ng-click="scrollTop()"
                 style="margin:10px;font-size:11px;font-weight:bold;opacity:.9;">
                <i class="fa fa-arrow-up"></i>
            </div>
        </div>
    </div>
    <table ng-class="{loaded:loaded}" <?= $this->expandAttributes($this->tableOptions); ?>>
        <thead>
        <tr>
            <?php foreach ($this->columns as $idx => $col) {
                echo $this->getHeaderTemplate($col, $idx);
            } ?>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat-start="row in datasource.data track by $index" ng-if="row.$type=='g'" lv="{{row.$level}}"
            class="g">
            <?php foreach ($this->columns as $idx => $col): ?>
                <?= $this->getGroupTemplate($col, $idx); ?>
            <?php endforeach; ?>
        </tr>
        <tr ng-repeat-end ng-if="!row.$type || row.$type == 'r' || (row.$type == 'a' && row.$aggr)"
            lv="{{row.$level}}" class="{{!!row.$type ? row.$type : 'r'}}">
            <?php foreach ($this->columns as $idx => $col): ?>
                <?= $this->getRowTemplate($col, $idx); ?>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
