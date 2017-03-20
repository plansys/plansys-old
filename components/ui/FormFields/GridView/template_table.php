<div class="thead">
   <?php echo $this->generateHeaders('class'); ?>
</div>
<table ng-class="{loaded:loaded}" <?= $this->expandAttributes($this->tableOptions); ?>>
   <thead>
       <?php echo $this->generateHeaders('tag'); ?>
   </thead>
   <tbody>
   <tr ng-repeat-start="row in datasource.data track by $index" ng-if="row.$type=='g'" lv="{{row.$level}}"
       class="g">
       <?php foreach ($this->columns as $idx => $col): ?>
           <?= $this->getGroupTemplate($col, $idx); ?>
       <?php endforeach; ?>
   </tr>
   <tr ng-repeat-end ng-if="!row.$type || row.$type == 'r' || (row.$type == 'a' && row.$aggr)"
       lv="{{row.$level}}" class="{{!!row.$type ? row.$type : 'r'}} {{rowStateClass(row)}}">
       <?php foreach ($this->columns as $idx => $col): ?>
           <?= $this->getRowTemplate($col, $idx); ?>
       <?php endforeach; ?>
   </tr>
   </tbody>
</table>
<script type="text/template" name="columnsnew"><?= json_encode($this->columns); ?></script>
