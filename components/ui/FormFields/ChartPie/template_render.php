<div ps-chart-pie id="<?= $this->name ?>" class="<?= $this->colClass ?>" >
	
	<data name="width" class="hide"><?= $this->chartWidth; ?></data>
	<data name="height" class="hide"><?= $this->chartHeight; ?></data>
	<data name="chartTitle" class="hide"><?= $this->chartTitle; ?></data>
	<data name="series" class="hide"><?= json_encode($this->series); ?></data>
	<data name="datasource" class="hide"><?= $this->datasource; ?></data>
	
	<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
</div>