<div class="toolbar">
     <div class="line-number" >
          <input type="text" class="input" select-on-click 
                 ng-keyup="gotoLine(active.code.cursor.row, $event)" 
                 ng-model="active.code.cursor.row">
     </div>
     <div class="separator"></div>
     <div class="status" style="color: {{ getStatusColor(active) }}" 
          tooltip="{{ getStatusTooltip(active) }}"
          tooltip-placement="right">
          <i class="fa {{ getStatusIcon(active) }}"></i>
          {{ active.code.status }} 
     </div>
     <div class="separator"></div>
</div>