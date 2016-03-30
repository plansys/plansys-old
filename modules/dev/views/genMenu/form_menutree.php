<div ng-if='!item.icon'
     ui-tree-handle 
     ng-click="selectToggle(this)"  
     ng-class="is_selected(this)">
    <div context-menu="formTreeOpen(this, $event, item)"
         data-target="FormTreeMenu">

        <span>
            <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
            <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>
        </span>
        {{ item.plansys ? 'Plansys:' : '' }} {{item.module}}
    </div>
</div>

<a ng-if='!!item.icon' target="iframe" 
   href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{item.class}}"
   ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
    <div style="margin:-1px 0px;padding:1px 0px;" 
         context-menu="formTreeOpen(this, $event, item)"
         data-target="FormTreeMenu">
        <i class="fa fa-sitemap fa-nm" 
           style="{{item.label == 'MainMenu' ? 'color:orange;' : ''}}"></i>
        <span>{{item.label}}</span>
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
    <li ng-repeat="item in item.items" ui-tree-node class='menu-list-item' 
        ng-include="'FormTree'">
    </li>
</ol>