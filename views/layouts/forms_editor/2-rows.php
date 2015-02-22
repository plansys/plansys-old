<div ui-layout options="{flow: 'row'}" 
     <?php if (@$editor): ?>class="form-builder-layout"<?php endif; ?>>
    <div id='row1' ng-class="{active: layout.name == 'row1'}"
         size="<?= @$row1['size'] . @$row1['sizetype'] ?>">
        <?php $active = 'row1'; include("layout_properties.php"); ?>
        <?= @$row1['content'] ?>
    </div>
    <div id='row2' ng-class="{active: layout.name == 'row2'}"
         size="<?= @$row2['size'] . @$row2['sizetype'] ?>">
        <?php $active = 'row2';include("layout_properties.php"); ?>
        <?= @$row2['content'] ?>
    </div>
</div>