<div class="treeview-node-item">
    <div class="treeview-caret">
        <i class="fa fa-caret-right"></i>
    </div>
    <div class="treeview-icon">
        <i class="{{ item.icon }}"></i>
    </div>
    <span>{{ item.title}}</span>
</div>

<ol ui-tree-nodes="" ng-model="item.items" class="treeview-node">
    <li ng-repeat="item in item.items" 
        ui-tree-node 
        collapsed="false"
        ng-include="$ctrl.data.itemLayoutUrl"></li>
</ol>