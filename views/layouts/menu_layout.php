
<div context-menu="openContextMenu(item, $event, this)" context-menu-disabled="isContextMenuDisabled(item)" data-target="ContextMenu<?= $class ?>">
    <a ui-tree-handle href="{{ getUrl(item)}}" 
       target="{{ getTarget(item)}}"
       ng-click="select(item, $event)"
       ng-class="isSelected(item)"
       class="angular-ui-tree-handle">
        <div ng-show="objectSize(item.items) > 0" class="pull-left" 
             style="width:20px;text-align:center;cursor:pointer;">
            <i ng-show="item.state == 'collapsed'" class="fa fa-caret-right"></i>
            <i ng-show="item.state != 'collapsed'" class="fa fa-caret-down"></i>
        </div>
        <div>
            <i style="pointer-events: none" ng-if="iconAvailable(item)"  class="fa fa-fw {{item.icon}}"></i>
            <span style="pointer-events: none" ng-bind-html="item.label"></span>
        </div>
        <div ng-if="item.active" ng-init="select(item)"></div>
    </a>
</div>

<div class="dropdown menu-tree" 
     oncontextmenu="$('.menu-sel').removeClass('active').removeClass('.menu-sel'); return false;" 
     id="ContextMenu<?= $class ?>">
    <ul class="dropdown-menu" role="menu">
        <li ng-repeat-start="menu in contextMenu track by $index" ng-if="menu.visible && !menu.hr">
            <a class="pointer" role="menuitem"
               ng-click="executeMenu(menu.click, item, $event)">
                <i class="{{menu.icon}}"></i> {{ menu.label}}
            </a>
        </li>
        <hr ng-if="menu.visible && !!menu.hr" ng-repeat-end/>
        <li ng-if="contextMenuDisabled" 
            style="padding:20px 0px;text-align:center;font-size:11px;color:#999;">
            ~ Menu Disabled ~
        </li>
    </ul>
</div>

<ol ui-tree-nodes ng-model="item.items">
    <li ng-repeat="item in item.items" ui-tree-node data-collapsed="item.state == 'collapsed'"
        ng-include="'<?= $class ?>MenuTreeLoop'"></li>
</ol>
