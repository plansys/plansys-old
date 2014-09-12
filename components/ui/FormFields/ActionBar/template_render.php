
<div class="action-bar" >
    <div class="title-bar">
        <span class="title"><?= $this->title ?></span>
    </div>
    <div class="link-bar">
        <?= $this->renderLinkBar ?>
    </div>
    <div class="clearfix"></div>
    <?php if ($this->showSectionTab == "Yes"): ?>
    <div class="action-tab" >
        <a href="#general" top="0" class="active">General</a>
        <div class="clearfix"></div>
    </div>
    <?php endif; ?> 
</div>
<div id="general"></div>