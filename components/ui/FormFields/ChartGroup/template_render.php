<div oc-lazy-load="[
     {files:['<?= Yii::app()->controller->staticUrl('/js/lib/highcharts/highcharts.js') ?>']}
     ]">
    <div ps-chart-group id="<?= $this->name ?>" class="col-md-12" >

        <data name="groupTitle" class="hide"><?= $this->title; ?></data>
        <data name="groupName" class="hide"><?= $this->name; ?></data>
        <data name="yAxisType" class="hide"><?= $this->yAxisType; ?></data>
        <data name="isPieGroup" class="hide"><?= $this->isPieGroup; ?></data>
        <data name="groupOptions" class="hide"><?= json_encode($this->extractJson($this->options)); ?></data>

        <?= $this->renderColumn(1); ?>
        <div id="groupContainer<?= $this->name ?>" ></div>
    </div>
    <div class="clearfix"></div>
</div>