
<div class="tcols-container" ng-show="freezedColsReady">
   <table class="tcols header" ng-class="{loaded:loaded}" >
      <?php $cols = $this->getFreezedCols(); ?>
      <thead>
         <?php echo $this->generateHeaders('tag', $cols); ?>
      </thead>
   </div>
   <table class="tcols data" ng-class="{loaded:loaded}">
      <tbody>
         <tr ng-repeat-start="row in datasource.data track by $index" ng-if="row.$type=='g'" lv="{{row.$level}}"
             class="g">
             <?php foreach ($cols as $idx => $col): ?>
                 <?= $this->getGroupTemplate($col, $idx); ?>
             <?php endforeach; ?>
         </tr>
         <tr ng-repeat-end ng-if="!row.$type || row.$type == 'r' || (row.$type == 'a' && row.$aggr)"
             lv="{{row.$level}}" class="{{!!row.$type ? row.$type : 'r'}} {{rowStateClass(row)}}">
             <?php foreach ($cols as $idx => $col): ?>
                 <?= $this->getRowTemplate($col, $idx); ?>
             <?php endforeach; ?>
         </tr>
      </tbody>
   </table>
</div>