<div class="btn-group" style="margin-right:7px;">

    <a class="btn btn-default btn-sm" ng-click="datePrev(filter, $event)" style="height:31px;" 
       ng-if="['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) >= 0">
        <i class="fa fa-chevron-left"></i>
    </a>
    <a class="btn btn-default btn-sm" ng-click="dateNext(filter, $event)" style="height:31px;"
       ng-if="['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) >= 0">
        <i class="fa fa-chevron-right"></i>
    </a>
    <a class="btn btn-default btn-sm" 
        ng-click="toggleDailyPicker(filter)"
        ng-if="filter.operator === 'Daily'" style="height:31px;">
        <i class="fa fa-calendar"></i>
    </a>
    <button type="button" ng-click="toggleFilterCriteria($event)" ng-disabled="filter.options.lockOperator"  
            class="btn btn-default btn-sm dropdown-toggle filter-criteria-button" 
            style="color:#333;opacity:1;height:31px;">
        <span style="font-size:13px;"> 
            {{filter.label}}<span 
                ng-hide="(filter.filterType == 'number'
                                && filter.valueText != 'All'
                                && filter.operator != 'Is Empty') || filter.label == ''
                ">:</span></span>
        <b>{{filter.valueText}}
        </b>
        <span style="margin-left:5px;display:inline-block;"></span><span class="caret" ng-if="!filter.options.lockOperator" ></span>
    </button>
    <button type="button" ng-click="resetFilter(filter)" ng-show="filter.valueText != 'All' && filter.resetable == 'Yes'"
            class="btn btn-inverse btn-sm filter-reset" >
        <i class="fa fa-times fa-nm" ></i>
    </button>
    <div ng-if="filter.daily.open" class="daily-picker" 
        style="
        min-width:190px;display:block;position:absolute;
        background:white;border:1px solid #aaa;margin-top:35px;
        z-index:999;padding:5px;border-radius:4px;
        box-shadow:0 6px 12px rgba(0,0,0,.175)" role="menu" > 
        <input type="text" ng-model="filter.daily.day" style="width:50px">
        <input type="text" ng-model="filter.daily.month" style="width:50px">
        <input type="text" ng-model="filter.daily.year" style="width:50px">
        <div class="btn btn-xs btn-default" ng-click="filterDaily(filter)"><i class="fa fa-play"></i></div>
    </div>
    <div class="dropdown-menu filter-criteria" style="min-width:190px;" role="menu" >  
        <div ng-if="['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) >= 0"
             style='float:left;margin:4px 5px 0px 0px;'
             >
            Filter Option:
        </div>
        <button ng-if="['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) < 0" 
                ng-click="updateFilter(filter, $event)" class="pull-right btn btn-sm btn-info" type="button">
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
                    ng-class="{ hover: operator == filter.operator }">
                    <a href="#" ng-click="dateChangeOperator(filter, operator, $event)" ng-bind-html="operator"></a>
                </li>
            </ul>
        </div>
        <center ng-show="filter.operator == 'Weekly'">
            <hr/><b>{{ filter.from | date: 'dd' }} - {{ filter.to | date:'dd (MMM yyyy)' }}</b>
        </center>

        <div ng-if="['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) < 0" 
             class="form-group-sm" 
             style="padding-top:8px;margin-left:-1px;white-space: nowrap; word-wrap: normal;">

            <input type="text" class="form-control" 
                   datepicker-popup='dd/MM/yyyy'
                   datepicker-options="dateOptions"
                   close-on-date-selection="false"
                   is-open ="filter.fromOpen"
                   ng-model="filter.from"
                   ng-if="['Between', 'Not Between', 'More Than'].indexOf(filter.operator) >= 0"
                   ng-focus="focusDatePicker(filter, 'from')"
                   ng-change="changeValueFromDate(filter, 'from')"
                   style="width:120px;margin-right:4px;display:inline-block;" placeholder="From" />


            <input type="text" class="form-control" 
                   datepicker-popup='dd/MM/yyyy'
                   datepicker-options="dateOptions"
                   close-on-date-selection="false"
                   is-open ="filter.toOpen"
                   ng-model="filter.to"
                   ng-if="['Between', 'Not Between', 'Less Than'].indexOf(filter.operator) >= 0"
                   ng-focus="focusDatePicker(filter, 'to')"
                   ng-change="changeValueFromDate(filter, 'to')"
                   style="width:120px;display:inline-block;" placeholder="To" />

            <div class="clearfix"></div>
        </div>

        <div class="filter-date-dropdown">

        </div>
        
    </div>
</div>
