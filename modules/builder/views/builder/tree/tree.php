<?php Asset::registerJS($this->vpath . '.tree'); ?>
<?php Asset::registerCSS($this->vpath . '.tree'); ?>
<script type="text/ng-template" id="treeItem"> 
     <?php include("tree-item.php"); ?>
</script>
<div ng-controller="Tree" class="tree-container" mode="<?= Setting::get('app.mode'); ?>">
     <div class="search-file">
          <input type="search" class="search-text" ng-delay="300"
                 ng-change="doSearch()" placeholder="Files" ng-model="search.text">
          <div class="icon" ng-click="resetSearch()">
               <i ng-if="!search.loading && !search.text" class="fa fa-search"></i>
               <b ng-if="!search.loading && !!search.text">&times;</b>
               <i ng-if="search.loading" class="fa fa-refresh fa-spin"></i>
          </div>
          <div class="arrow" ng-class="{active: search.detail.show}"
               ng-click="search.detail.show = !search.detail.show">
               <i class="fa fa-chevron-down" ng-if="!search.detail.show"></i>
               <i class="fa fa-chevron-up" ng-if="search.detail.show"></i>
          </div>
          <div class="search-file-detail" ng-if="search.detail.show">
               <div class="search-file-detail-head">TREE OPTIONS</div>
               <div class="search-file-detail-row">
                    Search&nbsp;Path
                    <input type="text" style="margin-left:5px"
                           ng-keydown="detailPathChanged($event)" ng-model="search.detail.path">
               </div>
               <div class="search-file-detail-row"> 
                    <div ng-click="search.detail.show = false; doSearch()" 
                         class="btn btn-xs btn-default btn-block">
                         OK
                    </div>
                </div>
          </div>
     </div>
     
     <div class="context-menu" 
          style="left:{{cm.pos.x}}px;top:{{cm.pos.y}}px" 
          ng-if="cm.active && !cm.hidden">
         <div class="dropdown">
              <ul class="dropdown-menu" role="menu">
                  <li ng-repeat-start="menu in cm.menu track by $index" 
                      ng-if="!menu.hr && (!menu.visible || (menu.visible && menu.visible(cm.active)))">
                      <a class="pointer" role="menuitem"
                         oncontextmenu="return false"
                         ng-click="cm.click($event, menu.click, menu)">
                          <i class="{{menu.icon}}"></i>
                           {{ cm.getLabel(menu) }}
                      </a>
                  </li>
                  <hr ng-if="!!menu.hr && (!menu.visible || (menu.visible && menu.visible(cm.active)))" ng-repeat-end/>
              </ul>
         </div>
     </div>
     <div class="context-menu-overlay" ng-if="cm.active" 
          oncontextmenu="return false"
          ng-mousedown="cm.active = null;cm.activeIdx = -1;"></div>
     
     <div ng-if="!search.text" class="tree">
          <div ng-repeat="item in tree" 
               ng-include="'treeItem'" 
               ng-if="showItem(item)"></div>
     </div>
     <div ng-if="search.text" class="tree search">
          <div ng-repeat="item in search.tree" 
               ng-include="'treeItem'"></div>
          
          <div class="tree-load-more" 
               ng-click="nextSearchResult()"
               ng-if="search.rawtree.length > search.tree.length">
               <div ng-if="!search.loading">
                    Load more <i class="fa fa-chevron-down"></i>
               </div>
          </div>
     </div>
</div>