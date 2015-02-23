<div ng-click="click(this)" ui-tree-handle ng-class="isSelected(this)">

    <div ng-show="item.items.length > 0" class="pull-left" 
         style="width:20px;text-align:center;cursor:pointer;">
        <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
        <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>
    </div>

    <div>
        <span ng-if="iconAvailable(item)" >
            <i class="fa fa-fw {{item.icon}}"></i> 
        </span>
        <span ng-bind-html="item.label"></span>
    </div>
</div>
<ol ui-tree-nodes ng-model="item.items">
    <li ng-repeat="item in item.items" data-collapsed="isCollapsed(item)" ui-tree-node ng-include="'<?= $class ?>MenuTreeLoop'"></li>
</ol>
