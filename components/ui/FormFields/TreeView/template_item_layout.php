<div style="cursor:pointer;">
    <div style="float:left;width:10px;" >
        <i class="fa fa-caret-right"></i>
    </div>
    <i class="{{ item.icon}}"></i>
    <span>{{ item.title}}</span>
</div>

<ol ui-tree-nodes="" ng-model="item.items" style="padding-left:10px;">
    <li ng-repeat="item in item.items" 
        ui-tree-node 
        collapsed="false"
        ng-include="$ctrl.data.itemLayoutUrl"></li>
</ol>