<?php Asset::registerJS($this->vpath . '.tree'); ?>
<?php Asset::registerCSS($this->vpath . '.tree'); ?>
<script type="text/ng-template" id="treeItem"> 
     <?php include("tree-item.php"); ?>
</script>
<script type="text" id="tree-expand-data">
     <?php echo $tree['expand']; ?>
</script>
<script type="text" id="tree-bar-data">
     <?php echo json_encode($this->treebar); ?>
</script>
<div ng-controller="Tree" class="tree-container" 
     treebar-active="<?= $tree['treebar']['active'];?>" 
     mode="<?= Setting::get('app.mode'); ?>">
     <div class="treebar">
          <div ng-repeat="tmode in treebar.list"
               ng-class="{active: tmode == treebar.active}"
               ng-click="treebar.switch(tmode)"
               class="tbtn" 
               tooltip="{{tmode | ucfirst}}s" tooltip-placement="bottom">
               <img ng-src="<?= $this->treeUri; ?>/treebar/{{tmode}}.png" />
          </div>
          <div class="overflow">
               <span><div class="fixed"></div></span>
          </div>
     </div>
     <div class="treesearch" ng-class="{detail:search.detail.show}">
          <div class="icon" ng-click="resetSearch()">
               <i ng-if="!search.loading && !search.text" class="fa fa-search"></i>
               <b ng-if="!search.loading && !!search.text">&times;</b>
               <i ng-if="search.loading" class="fa fa-refresh fa-spin"></i>
          </div>
          <input type="search" class="search-text" ng-delay="300"
                 ng-change="doSearch()" 
                 ng-model="search.text"
                 ng-focus="search.showDetail()"
                 placeholder="Search {{ treebar.active | ucfirst}} Name...">
          <div ng-if="search.detail.show">
               <div class="label label-default search-location-icon">IN PATH:</div>
               <div class="search-location"> {{ search.detail.path }} </div>
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
                           <span ng-bind-html="cm.getLabel(menu)"></span>
                      </a>
                  </li>
                  <hr ng-if="!!menu.hr && (!menu.visible || (menu.visible && menu.visible(cm.active)))" ng-repeat-end/>
              </ul>
         </div>
     </div>
     <div class="context-menu-overlay" ng-if="cm.active" 
          oncontextmenu="return false"
          ng-mousedown="cm.active = null;cm.activeIdx = -1;"></div>
     
     
     <div class="tree-empty" 
          ng-if="treebar.loading || treebar.tree[treebar.active].length == 0 || (search.text && search.tree.length == 0)">
          <br/><br/>
          <center>
               <img src="<?= $this->treeUri; ?>/treebar/tree.png" /><br/>
               <small ng-if="treebar.loading">Loading Tree</small>
               <small ng-if="!search.text && !treebar.loading">
                    {{ treebar.active | ucfirst }} is empty<br/>Plant some!
               </small>
               <small ng-if="search.text && !search.loading">
                    {{ treebar.active | ucfirst }} <u><b>{{ search.text }}</b></u>
                    <br/>not found!
               </small>
               <small ng-if="search.text && search.loading">
                    Searching {{ treebar.active }}s...
               </small>
          </center>
     </div>
     
     <?php foreach ($this->treebar as $item): ?>
     <div ng-show="!search.text && treebar.active == '<?= $item; ?>'" class="tree" 
          ng-class="{hovered: !!drag.lastHoverItem && drag.lastHoverItem == treebar.root.file}" 
          ng-mouseup="itemMouseUp($event, root)">
          <div ng-repeat="item in treebar.tree.<?= $item; ?>" 
               ng-include="'treeItem'" 
               ng-if="showItem(item)"></div>
          <br/><br/><br/>
     </div>
     <?php endforeach; ?>
     
     
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