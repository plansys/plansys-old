<div ps-chart-group id="<?= $this->name ?>" class="col-md-12" >
	
	<data name="groupTitle" class="hide"><?= $this->title; ?></data>
	<data name="groupName" class="hide"><?= $this->name; ?></data>
	<data name="yAxisType" class="hide"><?= $this->yAxisType; ?></data>
	<data name="isPieGroup" class="hide"><?= $this->isPieGroup; ?></data>
	<data name="groupOptions" class="hide"><?= json_encode($this->extractJson($this->options)); ?></data>
	
	<?= $this->renderColumn(1); ?>
	<div id="groupContainer<?= $this->name ?>" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
</div>