<div ps-chart-pie id="<?= $this->name ?>" class="<?= $this->colClass ?>" >
	
	<data name="width" class="hide"><?= $this->chartWidth; ?></data>
	<data name="height" class="hide"><?= $this->chartHeight; ?></data>
	<data name="datasource" class="hide"><?= $this->datasource; ?></data>
	
    <nvd3-pie-chart <?= $this->expandAttributes($this->options) ?>
      id="<?= $this->name ?>"
      data="chartData"
      x="xFunction()"
      y="yFunction()"
      width="{{width}}"
      height="{{height}}" >
      <svg></svg>
    </nvd3-pie-chart>
</div>