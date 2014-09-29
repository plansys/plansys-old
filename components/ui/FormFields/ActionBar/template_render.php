
<div class="action-bar-container">
    <div class="action-bar" >
        <div class="title-bar">
            <span class="title"><?= $this->title ?></span>
        </div>
        <div class="link-bar">
            <div ng-show='!formSubmitting'>
                <?= $this->renderLinkBar ?>
            </div>

            <div ng-show='formSubmitting'>
                <i class="fa fa-spin fa-refresh fa-lg" style='margin:10px 10px 0px 0px'></i>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php if ($this->showSectionTab == "Yes"): ?>
            <div class="action-tab" >
                <a href="#general" top="0" class="active">General</a>
                <div class="clearfix"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="general"></div>