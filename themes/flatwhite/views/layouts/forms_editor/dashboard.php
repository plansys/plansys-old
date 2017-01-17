<div ui-layout options="{flow: 'column',dividerSize:1}" 
     <?php if (@$editor): ?>class="form-builder-layout"<?php endif; ?>>
    <div ui-layout-container id='col1' ng-class="{active: layout.name == 'col1'}" size="100%">
        <?php $active = 'col1'; include("layout_properties.php"); ?>
        <?= @$col1['content'] ?>
    </div>
</div>