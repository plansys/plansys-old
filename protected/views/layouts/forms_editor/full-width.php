<div ui-layout options="{flow: 'column'}" 
     <?php if (@$editor): ?>class="form-builder-layout"<?php endif; ?>>
    <div id='col1' ng-class="{active: layout.name == 'col1'}"
         size="<?= @$col1['size'] ?>">
        <?php $active = 'col1'; include("layout_properties.php"); ?>
        <?= @$col1['content'] ?>
    </div>
</div>