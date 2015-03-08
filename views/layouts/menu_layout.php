<a href="{{ getUrl(item)}}" 
   target="{{ getTarget(item)}}"
   ng-click="select(item)" 
   class="angular-ui-tree-handle"
   ui-tree-handle ng-class="isSelected(item)">
    <div ng-show="item.items" class="pull-left" 
         style="width:20px;text-align:center;cursor:pointer;">
        <i ng-show="item.state == 'collapsed'" class="fa fa-caret-right"></i>
        <i ng-show="item.state != 'collapsed'" class="fa fa-caret-down"></i>
    </div>

    <div>
        <i ng-if="iconAvailable(item)"  class="fa fa-fw {{item.icon}}"></i><span ng-bind-html="item.label"></span>
    </div>
    <div ng-if="item.active" ng-init="select(item)"></div>
</a>
<ol ui-tree-nodes ng-model="item.items">
    <li ng-repeat="item in item.items" ui-tree-node data-collapsed="item.state == 'collapsed'"
        ng-include="'<?= $class ?>MenuTreeLoop'"></li>
</ol>
