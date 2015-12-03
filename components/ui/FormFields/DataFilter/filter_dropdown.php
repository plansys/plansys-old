<div class="btn-group" dropdown ng-click="dropdownClick(filter, $event)"
     style="margin-right:7px;"
     is-open="filter.operatorDropdownOpen">

    <button type="button" class="btn btn-default btn-sm dropdown-toggle">
        <span style="font-size:13px;"> 
            {{filter.label}}<span ng-hide="filter.label == ''">:</span></span>
        <b>{{filter.valueText | more:15}}</b>
        <span class="caret" style="margin-left:5px;"></span>
    </button>
    <button type="button" ng-click="resetFilter(filter);"
            ng-show="filter.valueText != 'All' && filter.resetable == 'Yes'"
            class="btn btn-inverse btn-sm filter-reset">
        <i class="fa fa-times fa-nm"></i>
    </button>
    <!-- dropdown item -->
    <div class="dropdown-menu open" style="font-size:13px;">
        <div class="search" ng-show="filter.searchable" style="margin-bottom:0px;">
            <input type="text"
                   ng-model="filter.search"
                   ng-change="listSearch($event, filter)"
                   ng-delay="500"
                   ng-keypress="dropdownSearchKeypress($event)"
                   ng-click="$event.preventDefault()"
                   placeholder="Search ..."
                   class="input-block-level search-dropdown form-control" autocomplete="off">

        </div>
        <div ng-if="filter.list.length == 0 && !loading"
             style="text-align:center;padding:15px;font-size:12px;color:#999;">
            &mdash; NOT FOUND &mdash;
        </div>
        <ul class="dropdown-menu inner data-filter-dropdown" style="overflow-x:hidden;max-height:240px;" role="menu">
            <li class="dropdown-item" ng-if="filter.relIncludeEmpty == 'Yes'">
                <a dropdown-toggle href="#" style="padding-top:6px;" ng-click="updateDropdown($event, filter, {
                        key: filter.relEmptyValue,
                        value: filter.relEmptyLabel
                    });">
                    {{ filter.relEmptyLabel}}
                </a>
            </li>
            <hr ng-if="filter.relIncludeEmpty == 'Yes'" style="margin:0px;"/>

            <li ng-repeat-start="item in filter.list track by $index "
                ng-if="item.value != '---'" class="dropdown-item"
                ng-class="{'dropdown-header': isObject(item.value),
                                    'hover': item.key == filter.value}"
                ng-show="filter.filterType == 'relation' || listFound(item.value + ' ' + item.key, filter)">

                <a ng-if="!isObject(item.value) &&
                                (filter.filterType == 'list' || filter.filterType == 'relation')"
                   dropdown-toggle href="#" ng-click="updateDropdown($event, filter, item);"
                   value="{{item.key}}">
                    {{ item.value}}
                </a>

                <a ng-if="!isObject(item.value) && filter.filterType == 'check'" href="#"
                   ng-click="updateDropdown($event, filter, item);"
                   value="{{item.key}}">

                    <label class="filter-dropdown-label" style="margin-left:-10px;">
                        <i class="fa fa-check-square-o fa-lg fa-fw" ng-if="dropdownChecked(filter, item)"></i>
                        <i class="fa fa-square-o fa-lg fa-fw" ng-if="!dropdownChecked(filter, item)"></i>
                        {{ item.value}}
                    </label>
                </a>

                <div ng-if="isObject(item.value)" class="dropdown-menu-submenu">
                    <div class="dropdown-menu-header">
                        <div class="dropdown-menu-header-line"></div>
                        <div class="dropdown-menu-header-text">{{item.key}}</div>
                    </div>

                    <ul class="dropdown-menu inner" role="menu"
                        style="display:block;border-radius:0px;">

                        <li ng-repeat-start="subitem in item.value track by $index "
                            ng-if="subitem.value != '---'"
                            ng-class="{'hover': subitem.key == filter.value}"
                            ng-show="filter.filterType == 'relation' || listFound(subitem.value + ' ' + subitem.key, filter)">

                            <a ng-if="!isObject(subitem.value) && filter.filterType == 'list'"
                               dropdown-toggle href="#" ng-click="updateDropdown($event, filter, subitem);"
                               value="{{subitem.key}}">
                                {{ subitem.value}}
                            </a>

                            <a ng-if="!isObject(subitem.value) && filter.filterType == 'check'" href="#"
                               ng-click="updateDropdown($event, filter, subitem);"
                               value="{{subitem.key}}">

                                <label class="filter-dropdown-label" style="margin-left:-10px;">
                                    <i class="fa fa-check-square-o fa-lg fa-fw"
                                       ng-if="dropdownChecked(filter, subitem)"></i>
                                    <i class="fa fa-square-o fa-lg fa-fw" ng-if="!dropdownChecked(filter, subitem)"></i>
                                    {{ subitem.value}}
                                </label>
                            </a>
                        </li>
                        <hr ng-repeat-end ng-if="subitem.value == '---'"/>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            </li>
            <hr ng-repeat-end ng-if="item.value == '---'"/>
            <hr ng-if="!loading && filter.filterType == 'relation' && filter.count > filter.list.length"/>
            <li ng-if="filter.filterType == 'relation' && filter.count > filter.list.length">
                <a href="#" ng-click="relationNext($event, filter)" style="margin-left:-5px;padding-bottom:10px;">
                    <span ng-if="!loading"><i class="fa fa-angle-down"></i> &nbsp; Load More</span>
                    <span ng-if="loading"><i class="fa fa-refresh fa-spin"></i> &nbsp; Loading... </span>
                </a>
            </li>
        </ul>
    </div>
</div>
