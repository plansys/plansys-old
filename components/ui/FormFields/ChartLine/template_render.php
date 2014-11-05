<div ps-chart-line id="<?= $this->name ?>" class="<?= $this->colClass ?>" >
	
	<data name="chartTitle" class="hide"><?= $this->chartTitle; ?></data>
	<data name="series" class="hide"><?= json_encode($this->series); ?></data>
	<data name="tickSeries" class="hide"><?= $this->tickSeries; ?></data>
	<data name="options" class="hide"><?= json_encode($this->extractJson($this->options)); ?></data>
	<data name="datasource" class="hide"><?= $this->datasource; ?></data>
	
	<div id="lineContainer" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
</div>