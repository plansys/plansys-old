    
<div color-picker <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->
		labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->renderID; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">
		<data name="value"><?=$this->color ?></data>
		<div class="input-group colorpicker">
        <!-- field -->
			<span class="input-group-addon"><i style="background-color: {{color}};"></i></span>
			<input type="<?= $this->fieldType ?>" <?= $this->expandAttributes($this->fieldOptions) ?>
                   ng-model="color" value="<?= $this->color ?>"/>
		
        <!-- /field -->
    </div>
</div>