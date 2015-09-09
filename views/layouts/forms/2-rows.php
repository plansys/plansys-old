<div ui-layout options="{flow: 'row',dividerSize:1}">
    <div ui-layout-container id='row1' class="container-full" size="<?= @$row1['size'] . @$row1['sizetype']?>?>">
        <div class="container-fluid" >
            <?= @$row1['content'] ?>
        </div>
    </div>
    <div ui-layout-container id='row2' size="<?= @$row2['size'] . @$row1['sizetype']?>">
        <div class="container-fluid">
            <?= @$row2['content'] ?>
        </div>
    </div>
</div>