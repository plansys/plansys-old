<div ace-editor <?= $this->expandAttributes($this->containerOptions); ?>>
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="attr" class="hide"><?= json_encode($this->options); ?></data>
    <a ng-click="popup()"
        style="margin:-2px 0px 2px 0px"
        class="btn btn-default btn-xs pull-right">
        <i class="fa fa-object-ungroup"></i>
        Edit PopUp
    </a>
    <?= $this->label ?>
    <div ui-ace="aceConfig({inline:true, mode: 'html'})"
        <?= $this->expandAttributes($this->options); ?>>
    </div>
</div>
