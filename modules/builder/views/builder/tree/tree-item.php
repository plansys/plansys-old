<div class="tree-item"
     ng-class='{
          active: selected.id == item.id,
          cm: cm.active.id == item.id
     }'
     oncontextmenu="return false"
     ng-mouseleave="itemMouseOut($event, item)"
     ng-mousedown="itemMouseDown($event, item)"
     ng-mousemove="itemMouseOver($event, item)"
     ng-mouseup="itemMouseUp($event, item)">
     <span class="ic">
          <i class="icon arrow fa {{ getArrow(item) }}"></i> 
          <i ng-if="item.loading" class="icon fa fa-spin fa-refresh"></i> 
          <div ng-if="!item.loading" class="icon">
               <img ng-src="<?= Yii::app()->request->getBaseUrl(true) ?>/plansys/modules/builder/views/builder/tree/icons/{{getIcon(item)}}" />
          </div>
     </span>
     
     <span class="text">{{ item.n }}</span>
</div>
<div ng-if="item.childs && item.expand" class="tree-childs">
     <div ng-repeat="item in item.childs" ng-if="showItem(item)" 
          ng-include="'treeItem'"></div>
</div>