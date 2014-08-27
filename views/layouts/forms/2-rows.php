<div ui-layout options="{flow: 'row'}">
    <div id='row1' class="container-full" size="<?= @$row1['size'] . @$row1['sizetype']?>?>">
        <div class="container-fluid" >
            <?= @$row1['content'] ?>
        </div>
    </div>
    <div id='row2' size="<?= @$row2['size'] . @$row1['sizetype']?>">
        <div class="container-fluid">
            <?= @$row2['content'] ?>
        </div>
    </div>
</div>