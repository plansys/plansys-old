

<div class="btn-group" dropdown ng-click="dropdownClick(filter,  $event)" 
     style="margin-right:7px;"
     is-open="filter.operatorDropdownOpen">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle">
        <span style="font-size:13px;"> 
            {{filter.label}}<span>:</span></span>
        <b>{{filter.valueText}}</b>


        <span class="caret" style="margin-left:5px;"></span>
    </button>
    <button type="button" ng-click="resetFilter(filter);" ng-show="filter.valueText != 'All'"
            class="btn btn-inverse btn-sm filter-reset" >
        <i class="fa fa-times fa-nm" ></i>
    </button>
    <!-- dropdown item -->
    <div class="dropdown-menu open" style="font-size:13px;">
        <div class="search" ng-show="filter.searchable">
            <input type="text"
                   ng-model="filter.search"
                   ng-keydown="listSearch($event, filter)"
                   placeholder="Search ..."
                   class="input-block-level search-dropdown form-control" autocomplete="off">
        </div>
        <ul class="dropdown-menu inner" role="menu">
            <li ng-repeat-start="item in filter.list track by $index" 
                ng-if="item.value != '---'" class="dropdown-item" 
                ng-class="{
                            'dropdown-header': isObject(item.value),
                                    'hover': item.key == filter.value
                            }"
                ng-show="listFound(item.value + ' ' + item.key, filter)">

                <a ng-if="!isObject(item.value) &&
                                    (filter.filterType == 'list' || filter.filterType == 'relation')"
                   dropdown-toggle href="#" ng-click="updateDropdown($event, filter, item);"
                   value="{{item.key}}">
                    {{ item.value}}
                </a>

                <a ng-if="!isObject(item.value) && filter.filterType == 'check'" href="#" 
                   ng-click="updateDropdown($event, filter, item);"
                   value="{{item.key}}">

                    <label class="filter-dropdown-label">
                        <input type="checkbox" ng-checked="dropdownChecked(filter, item)" />
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
                        <li ng-repeat-start="subitem in item.value track by $index" 
                            ng-if="subitem.value != '---'"
                            ng-class="{'hover': subitem.key == filter.value}"
                            ng-show="listFound(subitem.value + ' ' + subitem.key, filter)">

                            <a ng-if="!isObject(subitem.value) && filter.filterType == 'list'"
                               dropdown-toggle href="#" ng-click="updateDropdown($event, filter, subitem);"
                               value="{{subitem.key}}">
                                {{ subitem.value}}
                            </a>

                            <a ng-if="!isObject(subitem.value) && filter.filterType == 'check'" href="#" 
                               ng-click="updateDropdown($event, filter, subitem);"
                               value="{{subitem.key}}">

                                <label class="filter-dropdown-label">
                                    <input type="checkbox" ng-checked="dropdownChecked(filter, subitem)" />
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
        </ul>
    </div>
</div>
