<?php Asset::registerJS($this->vpath . '.tabs'); ?>
<?php Asset::registerCSS($this->vpath . '.tabs'); ?>
<div ng-controller="Tabs" class="tabs-container">
     <ul class="tabs" ng-if="list.length > 0">
          <li class="tab" 
               ng-class="{
                   active: active.id == item.id,
                   first: $index == 0,
                   unsaved: !!item.unsaved || item.loading,
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
                    <i class="fa fa-circle tab-icon-loading"></i>
                    <span class="tab-icon-x">&times;</span>
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
     <?php chdir(Yii::getPathOfAlias(ContentMode::$alias)); ?>
     <div class="content-container">
          <?php foreach ($this->mode->list as $mode): ?>
          <div id="<?= $mode ?>-container" ng-show="active.mode == '<?= $mode; ?>'">
               <?php include("{$mode}/index.php"); ?>
          </div>
          <?php endforeach; ?>
     </div>
     <div class="cant-edit-overlay" ng-if="(!active.editing && list.length > 0) || (!!active && (!canEdit(active) || active.loading))">
          <div class="cant-edit-box">
               <span ng-if="builder.statusbar.people && !!active.editing && !editRequest[active.id] && !canEdit(active)">
                    <center><img src="<?= $this->tabsUri; ?>/icons/sleeping.png"></center>
                    <div style="margin:10px;">
                         {{ getPeopleName(active.editing, true) | ucfirst }} 
                         {{ !canEdit(active) ? 'is now editing this file' : '' }}
                    </div>
                    <div ng-click="requestEdit(active)"
                         class="btn btn-default status-editing-request ">
                         <i class="fa fa-hand-pointer-o"></i> Take Over
                    </div>
               </span>
               <span ng-if="builder.statusbar.people && !!active.editing && editRequest[active.id]">
                    <center><img src="<?= $this->tabsUri; ?>/icons/coffee.png"></center>
                    <div style="margin:10px;">
                         Taking over edit from {{ getPeopleName(active.editing, true) | ucfirst }}... 
                    </div>
               </span>
               <span ng-if="!active.editing || active.loading || !builder.statusbar.people">
                    <center><?php include("icons/loading.html"); ?></center>
                    <div ng-if="!builder.ws || builder.statusbar.people.length == 0">Connecting...</div>
               </span>
          </div>
     </div>
     <div class="status-bar" ng-class="{dark: active.mode == 'code'}">
          <div class="left">
               <div ng-if="!!active && active.editing" class="status-editing status-item" tooltip="{{ editingTooltip(active) }} ">
                    <i class="fa {{ editingIcon(active) }} status-editing-icon"></i> 
                    <span ng-if="!canEdit(active)">
                         {{ getPeopleName(active.editing, true) | ucfirst }} 
                         is now editing this file
                    </span>
                    <span ng-if="canEdit(active)">
                          {{ active.p.split("/").pop() }}
                    </span>
               </div>
          </div>
          <div class="right">
               <div class="status-people status-item">
                    <div class="status-people-chat" ng-click="showChat()"
                         ng-class="{ 
                              show: builder.statusbar.chatshow,
                              peek: builder.statusbar.chatpeek
                         }">
                         <div class="status-people-msg">
                              <div class="status-people-msg-list">
                                   <div ng-repeat="msg in builder.statusbar.msg" 
                                        class="status-people-msg-item"
                                        ng-class="{me: builder.statusbar.me.cid == msg.f.cid}">
                                        <small class="status-people-msg-item-name">
                                             <div class="pull-left">{{ getPeopleName(msg.f) }}</div>
                                             <div class="pull-right">{{ msg.d }}</div>
                                        </small><div class="clearfix"></div>
                                        {{ msg.m }}
                                   </div>
                              </div>
                              <input type="text" class="status-people-msg-write" ng-keydown="builder.statusbar.sendmsg($event)" />
                         </div>
                         <div class="status-people-list">
                              <div ng-repeat="p in builder.statusbar.people" class="status-people-list-item">
                                   {{ getPeopleName(p) }}
                              </div>
                         </div>
                    </div>
                    <div ng-click="builder.statusbar.chatshow = !builder.statusbar.chatshow" style="user-select:none;">
                         
                         <span ng-if="builder.statusbar.people.length > 0">
                              <i class="fa fa-vcard" style="margin-right:5px;"></i> 
                              <span ng-if="builder.statusbar.people.length == 1">
                                   You are alone
                              </span>
                              <span ng-if="builder.statusbar.people.length > 1">
                                   {{ builder.statusbar.people.length }} connected
                              </span>
                         </span>
                         <span ng-if="builder.statusbar.people.length == 0">
                              <i class="fa fa-ban" style="margin-right:5px;"></i> 
                              Disconnected
                         </span>
                    </div>
               </div>
               <div class="status-connected status-item" 
                    tooltip-placement="left"
                    tooltip="{{builder.statusbar.connected ? 'Connected' : 'Disconnected'}}">
                    <i class="fa fa-circle connected" ng-if="builder.statusbar.connected"></i>
                    <i class="fa fa-circle disconnected" ng-if="!builder.statusbar.connected"></i>
               </div>
          </div>
     </div>
</div>