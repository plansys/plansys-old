<div oc-lazy-load="[
     {files:['<?= Yii::app()->controller->staticUrl('/js/lib/highcharts/highcharts.js') ?>']}
     ]">
    <div ps-chart-pie id="<?= $this->name ?>" class="<?= $this->colClass ?>" >
        
        <data name="chartTitle" class="hide"><?= $this->chartTitle; ?></data>
        <data name="chartType" class="hide"><?= $this->chartType; ?></data>
        <data name="chartName" class="hide"><?= $this->name; ?></data>
        <data name="series" class="hide"><?= json_encode($this->series); ?></data>
        <data name="options" class="hide"><?= json_encode($this->extractJson($this->options)); ?></data>
        <data name="datasource" class="hide"><?= $this->datasource; ?></data>
        
        <div id="<?= strtolower($this->chartType); ?>Container<?= $this->name; ?>"></div>
    </div>
    <div class="clearfix"></div>
</div>