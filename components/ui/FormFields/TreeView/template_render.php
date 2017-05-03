<div tree-view <?= $this->expandAttributes($this->options) ?>>
     <script type="text/template" name="data" class="hide"><?= @json_encode($this->data); ?></script>
     <script type="text/template" name="map" class="hide"><?= @json_encode($this->itemMap); ?></script>
     <script type="text/template" name="id" class="hide"><?= $this->renderID ?></script>
     <data name="name" class="hide"><?= $this->name; ?></data>
     <data name="class_alias" class="hide"><?= Helper::classAlias($model) ?></data>
     <script type="text/ng-template" id="treeitem<?= $this->renderID ?>"> 
          <?php include("treeitem.php"); ?>
     </script>
     
     <div class="tree-view-box" id="treeviewbox<?= $this->renderID ?>">
          <div ng-repeat="item in tree" ng-include="'treeitem<?= $this->renderID ?>'"></div>
     </div>
     
     <?php if ($this->debug == "ON"): ?>
     <hr/>
     <pre style="font-size:10px;overflow-x:scroll;"><div style="display:inline-block;width:3000px;">{{ flattenTree() | json }}</div></pre>
     <?php endif; ?>
</div>