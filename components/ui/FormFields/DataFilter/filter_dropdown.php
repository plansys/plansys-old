

<div class="btn-group" dropdown ng-click="focused($event)" is-open="filter.operatorDropdownOpen">
    <button type="button" class="btn btn-default dropdown-toggle">
        <span class="operator">{{ filter.value}}</span> 
        <span class="caret" style="margin-left:3px;"></span>
    </button>
    <!-- dropdown item -->
    <div class="dropdown-menu open" style="font-size:13px;">
        <div class="search" ng-show="filter.searchable">
            <input type="text"
                   ng-model="filter.search"
                   ng-keydown="listSearch($event)"
                   placeholder="Search ..."
                   class="input-block-level search-dropdown form-control" autocomplete="off">
        </div>
        <ul class="dropdown-menu inner" role="menu">
            <li ng-repeat-start="item in filter.list track by $index" 
                ng-if="item.value != '---'" class="dropdown-item" 
                ng-class="{'dropdown-header': isObject(item.value)}"
                ng-show="listFound(item.value + ' ' + item.key, filter)">
                <a ng-if="!isObject(item.value)"
                   dropdown-toggle href="#" 
                   ng-click="update(item.key);"
                   value="{{item.key}}">
                    {{ item.value}}
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
                            ng-show="listFound(subitem.value + ' ' + subitem.key, filter)">

                            <a ng-if="!isObject(subitem.value)"
                               dropdown-toggle href="#" 
                               ng-click="update(subitem.key);"
                               value="{{subitem.key}}">
                                {{ subitem.value}}
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
