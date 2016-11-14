<div class="btn-group" style="margin-right:7px;">
    <button type="button" ng-click="toggleFilterCriteria($event)" 
            class="btn btn-default btn-sm dropdown-toggle filter-criteria-button" style="color:#333;">

        <span style="font-size:13px;"> 
            {{filter.label}}<span 
                ng-hide="(filter.filterType == 'number' 
                            && filter.valueText != 'All'
                            && filter.operator != 'Is Empty'
                            && filter.operator != 'Is Not Empty') || filter.label == ''
                ">:</span></span>
        <b>{{filter.valueText}}
        </b>

        <span class="caret" style="margin-left:5px;"></span>
    </button>
    <button type="button" ng-click="resetFilter(filter)" ng-show="filter.valueText != 'All' && filter.resetable == 'Yes'"
            class="btn btn-inverse btn-sm filter-reset" >
        <i class="fa fa-times fa-nm" ></i>
    </button>

    <div class="dropdown-menu filter-criteria" role="menu" >       
        <div class="input-group input-group-sm" >
            <div class="input-group-btn" dropdown ng-click="focused($event)" is-open="filter.operatorDropdownOpen">
                <button type="button" class="btn btn-default dropdown-toggle">
                    <span class="operator">{{ filter.operator}}</span> 
                    <span class="caret" style="margin-left:3px;"></span>
                </button>
                <ul class="dropdown-menu" role="menu" style="font-size:13px;"> 
                    <li ng-repeat="operator in operators[filter.filterType]" 
                        ng-class="{
                                    hover: operator == filter.operator
                                }">
                        <a href="#" ng-click="changeOperator(filter, operator, $event)" ng-bind-html="operator"></a>
                    </li>
                </ul>
            </div>
            <input type="text" ng-hide="filter.operator == 'Is Empty' || filter.operator == 'Is Not Empty'"
                   ng-model="filter.value" 
                   ng-keydown="filterValueKeydown(filter, $event)"
                   class="focused form-control" style="width:250px;">

            <div class="input-group-btn">
                <button ng-click="updateFilter(filter, $event)" class="btn btn-info" type="button">
                    <b>Update 
                        <i class="fa fa-angle-right"></i>
                    </b>
                </button>
            </div>
        </div><!-- /input-group -->
    </div>
</div>
