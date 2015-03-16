<div ng-if='!item.class' class="sidebar-tree" ui-tree-handle
     ng-click="toggle(this);
         select(this, item);"
     ng-class="isSelected(this)">
    <div style="margin:-1px 0px;padding:1px 0px;" 
         context-menu="formTreeOpen(this, $event, item)"
         data-target="FormTreeMenu">
        <!--
        <div class="ui-tree-handle-info" ng-if="!isNaN(item.count)">
            {{item.count ? item.count : item.items.length }} form{{item.items.length > 1 ? 's' : ''}}
        </div>
        -->
        <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
        <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>

        {{ getType(item) == "module" ? item.module : ''}}
        {{item.name}} 
    </div>
</div>

<a ng-if='item.class' target="iframe"
   href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{item.alias}}"
   ui-tree-handle ng-click="select(this)" ng-class="isSelected(this)">
    <div style="margin:-1px 0px;padding:1px 0px;" 
         context-menu="formTreeOpen(this, $event, item)"
         data-target="FormTreeMenu">
        <i class="fa {{ getIcon(item) }} fa-nm"></i>
        {{item.name}}
    </div>
</a>
<div class="dropdown menu-tree" id="FormTreeMenu">
    <ul class="dropdown-menu" role="menu">
        <li ng-repeat-start="menu in formTreeMenu track by $index" ng-if="!menu.hr">
            <a class="pointer" role="menuitem"
               oncontextmenu="return false"
               ng-click="executeMenu($event, menu.click)">
                <i class="{{menu.icon}}"></i> {{ menu.label}}
            </a>
        </li>
        <hr ng-if="menu.hr" ng-repeat-end/>
    </ul>
</div>
<ol ui-tree-nodes="" ng-model="item.items">
    <li ng-if="item.name" ui-tree-node collapsed="!this.collapsed ? true : this.collapsed"
        ng-repeat="(dir, item) in item.items"
        ng-include="'FormTree'"
        ui-tree-node>
    </li>
</ol>