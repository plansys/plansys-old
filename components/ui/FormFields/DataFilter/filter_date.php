<div class="btn-group" style="margin-right:7px;">
    <button type="button" ng-click="toggleFilterCriteria($event)" 
            class="btn btn-default btn-sm dropdown-toggle filter-criteria-button" style="color:#666;">

        <span style="font-size:13px;"> 
            {{filter.label}}<span 
                ng-hide="filter.filterType == 'number'
                                && filter.valueText != 'All'
                                && filter.operator != 'Is Empty'
                ">:</span></span>
        <b>{{filter.valueText}}
        </b>

        <span class="caret" style="margin-left:5px;"></span>
    </button>
    <button type="button" ng-click="resetFilter(filter)" ng-show="filter.valueText != 'All'"
            class="btn btn-inverse btn-sm filter-reset" >
        <i class="fa fa-times fa-nm" ></i>
    </button>

    <div class="dropdown-menu filter-criteria" style="min-width:190px;" role="menu" >  

        <button ng-click="updateFilter(filter, $event)" class="pull-right btn btn-sm btn-info" type="button">
            <b>Update 
                <i class="fa fa-angle-right"></i>
            </b>
        </button>     
        <div class="btn-group" dropdown ng-click="focused($event)" is-open="filter.operatorDropdownOpen">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle">
                <span class="operator">{{ filter.operator}}</span> 
                <span class="caret" style="margin-left:3px;"></span>
            </button>
            <ul class="dropdown-menu" role="menu" style="font-size:13px;"> 
                <li ng-repeat="operator in operators[filter.filterType]" 
                    ng-class="{
                                hover: operator == filter.operator
                            }">
                    <a href="#" ng-click="changeOperator(filter, operator, $event)">{{ operator}}</a>
                </li>
            </ul>
        </div>

        <div class="form-group-sm" style="padding-top:8px;margin-left:-1px;white-space: nowrap; word-wrap: normal;">
            <input type="text" class="form-control" 
                   datepicker-popup='dd/MM/yyyy'
                   datepicker-options="dateOptions"
                   close-on-date-selection="false"
                   is-open ="filter.fromOpen"
                   ng-model="filter.from"
                   ng-if="filter.operator != 'Less Than'"
                   ng-focus="focusDatePicker(filter, 'from')"
                   ng-change="changeValueFromDate(filter, 'from')"
                   style="width:120px;margin-right:4px;display:inline-block;" placeholder="From" />


            <input type="text" class="form-control" 
                   datepicker-popup='dd/MM/yyyy'
                   datepicker-options="dateOptions"
                   close-on-date-selection="false"
                   is-open ="filter.toOpen"
                   ng-model="filter.to"
                   ng-if="filter.operator != 'More Than'"
                   ng-focus="focusDatePicker(filter, 'to')"
                   ng-change="changeValueFromDate(filter, 'to')"
                   style="width:120px;display:inline-block;" placeholder="To" />

            <div class="clearfix"></div>
        </div>

        <div class="filter-date-dropdown">

        </div>


    </div>
</div>
