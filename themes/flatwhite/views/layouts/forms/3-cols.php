<div ui-layout options="{flow: 'column',dividerSize:1}">
    <div ui-layout-container id='col1' size="<?= @$col1['size'] ?>">
        <div class="container-fluid"><?= @$col1['content'] . @$col1['sizetype'] ?></div>
    </div>
    <div ui-layout-container id='col2' size="<?= @$col2['size'] ?>">
        <div class="container-fluid"><?= @$col2['content'] . @$col2['sizetype'] ?></div>
    </div>
    <div ui-layout-container id='col3' size="<?= @$col3['size'] ?>">
        <div class="container-fluid"><?= @$col3['content'] . @$col3['sizetype'] ?></div>
    </div>
</div>