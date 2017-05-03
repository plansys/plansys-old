<div class="tree-view-item"
     ng-class='{
          "active": selected[map.id] == item[map.id],
          "can-expand": item[map.canExpand]
     }'
     oncontextmenu="return false"
     ng-mouseleave="itemMouseOut($event, item)"
     ng-mousedown="itemMouseDown($event, item)"
     ng-mousemove="itemMouseOver($event, item)"
     ng-mouseup="itemMouseUp($event, item)">
          <i ng-if="item[map.canExpand]" class="tree-view-arrow fa {{ getArrow(item) }}"></i> 
          <span ng-if="!item.$loading && item.icon" class="tree-view-icon">
          </span>
          <span ng-if="item.$loading" class="tree-view-icon">
               <i class="fa fa-spin fa-refresh"></i> 
          </span>
     <span class="tree-view-text" ng-bind-html="item[map.label]"></span>
</div>

<div ng-if="item[map.items] && item.$expand" class="tree-view-childs">
     <div ng-repeat="item in item.items" 
          ng-include="'treeitem<?= $this->renderID ?>'"></div>
</div>
