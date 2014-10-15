<div modal-dialog <?= $this->expandAttributes($this->options) ?>>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="render_id" class="hide"><?= $this->renderID; ?></data>
    <div style="display:none;" class="modal-container <?= $this->renderID ?>">
        <div tabindex="-1" role="dialog" class="modal-backdrop fade in"
             index="0" animate="animate" ng-click="close();"></div>

        <div class="modal-dialog" style="z-index:1100;">
            <div class="modal-content">
                <div class="modal-body">
                    <?= $this->renderSubForm() ?>
                    <div class="clearfix">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>