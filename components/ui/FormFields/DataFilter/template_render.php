<div ps-data-filter class="data-filter">
    <data name="filters" class="hide"><?= str_replace(
        ['&quot;','&lt;','&gt;'], 
        ['%quot%','%lt%','%gt%'],
        json_encode($this->filters)); ?></data>
    <data name="operators" class="hide"><?= json_encode($this->filterOperators); ?></data>
    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="datasources" class="hide"><?= json_encode($this->datasources()); ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="options" class="hide"><?= json_encode($this->options); ?></data>
    <data name="relperpage" class="hide"><?= ActiveRecord::DEFAULT_PAGE_SIZE ?></data>

    <script type="text/ng-template" id="filter_text"><?php include('filter_text.php'); ?></script>
    <script type="text/ng-template" id="filter_date"><?php include('filter_date.php'); ?></script>
    <script type="text/ng-template" id="filter_dropdown"><?php include('filter_dropdown.php'); ?></script>

    <table style="width:100%">
        <tr>
            <td class="filter-td filter-manage">
                <div class="btn-group" dropdown>
                    <button  type="button" class="btn btn-sm btn-default dropdown-toggle">
                        <i class="fa fa-bars fa-nm"></i> 
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" style="max-height:350px;overflow-y:auto;">
                        <li ng-if="ngIf(filter)" ng-repeat="filter in filters">
                            <a href="" ng-click="toggleShowFilter(filter)">
                                <label>
                                    <i class="fa fa-check-square-o fa-lg fa-fw" ng-if="filter.show" ></i>
                                    <i class="fa fa-square-o fa-lg fa-fw" ng-if="!filter.show" ></i>
                                    {{filter.label}}
                                </label>
                            </a>
                        </li>
                        <li ng-click="reset()" style="border-top:1px solid #ddd;margin-top:5px">
                            <a href=""><label>
                             Reset Page
                            </label></a>
                        </li>
                    </ul>
                </div>
            </td>
            <td class="filter-td" style="width:100%">
                <div class="filter-item-container" ng-repeat="filter in filters" ng-if="filter.show">
                    <div ng-if="ngIf(filter)" ng-include="filterTemplate[filter.filterType]"></div>
                </div>
            </td>
        </tr>
    </table>
    <div class="clearfix"></div>
    <input type="hidden" name="<?= $this->name; ?>" ng-value="filters | json">
</div>