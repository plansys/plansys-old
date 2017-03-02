<?php Asset::registerJS($this->vpath . '.tabs'); ?>
<?php Asset::registerCSS($this->vpath . '.tabs'); ?>
<div ng-controller="Tabs" class="tabs-container">
     <ul class="tabs" ng-if="list.length > 0">
          <li class="tab" 
               ng-class="{
                   active: active.id == item.id,
                   first: $index == 0,
                   unsaved: !!item.unsaved,
                   dragging: drag.item.id == item.id
               }"
               oncontextmenu="return false"
               ng-mouseleave="itemMouseOut($event, item)"
               ng-mousedown="itemMouseDown($event, item, $index)"
               ng-mousemove="itemMouseOver($event, item, $index)"
               ng-mouseup="itemMouseUp($event, item, $index)"
               ng-repeat="item in list">
               <div class="title" ng-mousedown="open(item)">{{ item.n }}</div>
               <div class="x" ng-click="close(item, $event);">
                    <i ng-if="item.loading" class="fa fa-spin fa-refresh"></i>
                    <span ng-if="!item.loading">&times;</span>
               </div>
          </li> 
     </ul>
     <div class="context-menu" 
          style="left:{{cm.pos.x}}px;top:{{cm.pos.y}}px" 
          ng-if="cm.active && !cm.hidden">
         <div class="dropdown">
              <ul class="dropdown-menu" role="menu">
                  <li ng-repeat-start="menu in cm.menu track by $index" 
                      ng-if="!menu.hr && (!menu.visible || (menu.visible && menu.visible(cm.active)))">
                      <a class="pointer" role="menuitem"
                         oncontextmenu="return false"
                         ng-click="cm.click($event, menu.click)">
                          <i class="{{menu.icon}}"></i>
                           {{ menu.label }}
                      </a>
                  </li>
                  <hr ng-if="!!menu.hr && (!menu.visible || (menu.visible && menu.visible(cm.active)))" ng-repeat-end/>
              </ul>
         </div>
     </div>
     <div class="context-menu-overlay" ng-if="cm.active" 
          oncontextmenu="return false"
          ng-mousedown="cm.active = null;cm.activeIdx = -1;"></div>
     <div class="toolbar" ng-if="active && active.mode == 'code'">
          <div ng-click="selectInTree(active)" ng-touchstart="selectInTree(active)" 
               tooltip="Select in tree" tooltip-placement="left"
               class="btn btn-xs btn-default select-in-tree">
               <i class="fa fa-exchange"></i>
          </div>
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
     <div class="content-container content">
          <div class="content">
               <div ng-show="active.mode == 'code'">
                    <?php 
                    chdir(Yii::getPathOfAlias("application.modules.builder.views.builder"));
                    include("code/code.php"); 
                    ?>
               </div>
          </div>
     </div>
</div>