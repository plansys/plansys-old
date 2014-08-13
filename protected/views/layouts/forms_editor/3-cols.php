<div ui-layout options="{flow: 'column'}" 
     <?php if (@$editor): ?>class="form-builder-layout"<?php endif; ?>>
    <div id='col1' ng-class="{active: layout.name == 'col1'}"
         size="<?= @$col1['size'] . @$col1['sizetype']  ?>">
        <?php $active = 'col1'; include("layout_properties.php"); ?>
        <?= @$col1['content'] ?>
    </div>
    <div id='col2' ng-class="{active: layout.name == 'col2'}"
         size="<?= @$col2['size'] . @$col2['sizetype']  ?>">
        <?php $active = 'col2'; include("layout_properties.php"); ?>
        <?= @$col2['content'] ?>
    </div>
    <div id='col3' ng-class="{active: layout.name == 'col3'}"
         size="<?= @$col3['size'] . @$col3['sizetype']  ?>">
        <?php $active = 'col3'; include("layout_properties.php"); ?>
        <?= @$col3['content'] ?>
    </div>
</div>