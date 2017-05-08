<div class="toolbar">
     <div class="line-number" >
          <input type="text" class="input" select-on-click 
                 ng-keydown="code.gotoLine(active.code.cursor.row, $event)" 
                 ng-model="active.code.cursor.row">
     </div>
     <div class="separator"></div>
     <div class="save-btn btn btn-xs btn-default" ng-click="save()">
          <i class="fa fa-floppy-o" ng-if="!active.loading"></i>
          <i class="fa fa-refresh fa-spin" ng-if="active.loading"></i>
     </div>
     <div  class="status" ng-click="save()">
          {{ active.code.status }}
     </div>
     <div class="separator"></div>
</div>