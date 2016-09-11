<div class="tv-node-item">
    <div class="tv-caret">
        <i class="fa fa-caret-right"></i>
    </div>
    <div class="tv-icon">
        <i class="{{ item.icon }}"></i>
    </div>
    <span>{{ item.title}}</span>
</div>

<ol ui-tree-nodes="" ng-model="item.items" class="tv-node">
    <li ng-repeat="item in item.items" 
        ui-tree-node 
        collapsed="false"
        ng-include="$ctrl.data.itemLayoutUrl"></li>
</ol>