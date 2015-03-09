
<div context-menu="openContextMenu(item, $event)" data-target="ContextMenu<?= $class ?>">
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
</div>

<div class="dropdown menu-tree" id="ContextMenu<?= $class ?>">
    <ul class="dropdown-menu" role="menu">
        <li ng-repeat-start="menu in contextMenu track by $index" ng-if="!menu.hr">
            <a class="pointer" role="menuitem"
               oncontextmenu="return false"
               ng-click="executeMenu($event, menu.click)">
                <i class="{{menu.icon}}"></i> {{ menu.label}}
            </a>
        </li>
        <hr ng-if="menu.hr" ng-repeat-end/>
    </ul>
</div>

<ol ui-tree-nodes ng-model="item.items">
    <li ng-repeat="item in item.items" ui-tree-node data-collapsed="item.state == 'collapsed'"
        ng-include="'<?= $class ?>MenuTreeLoop'"></li>
</ol>
