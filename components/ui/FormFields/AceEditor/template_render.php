<div ace-editor <?= $this->expandAttributes($this->containerOptions); ?>>
    <data name="name" class="hide"><?= $this->name ?></data>
    <?= $this->label ?>
    <div ui-ace="aceConfig({inline:true, mode: 'html'})"
        <?= $this->expandAttributes($this->options); ?>>
    </div>
</div>
