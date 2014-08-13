<div radio-button-list <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" style="padding-top:5px;">
        <!-- data -->
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list) ?></data>
        <!-- /data -->

        <!-- field -->
        <?php if ($this->itemLayout == "ButtonGroup") { ?>
            <div class="btn-group">
            <?php } ?>

            <?php
            if (is_array($this->list)) {
                foreach ($this->list as $value => $text):
                    ?>
                    <?php if ($this->itemLayout == "ButtonGroup"): ?>
                        <label class="radio-btn btn btn-sm btn-default" id="<?= $this->renderID ?>_<?= $value ?>" 
                               btn-radio="'<?= $value; ?>'" value="<?= $value; ?>" 
                               uncheckable ng-model="value"><?= $text; ?></label>
                    <?php else: ?>
                        <label class="radio-btn input-group <?= $this->itemLayout == "Horizontal" ? 'inline' : '' ?>">
                            <input type="radio" id="<?= $this->renderID ?>_<?= $value ?>"
                                   name="<?= $this->name ?>" value="<?= $value; ?>" <?= $this->checked($value); ?>
                                   /> <?= $text ?>
                        </label>
                    <?php endif; ?>
                    <?php
                endforeach;
            }
            ?>

            <?php if ($this->itemLayout == "ButtonGroup") { ?>
            </div>
        <?php } ?>   

        <input type="text" class="invisible"
               ng-model="value" id="<?= $this->renderID ?>"
               name="<?= $this->name ?>" value='<?= $this->value ?>'/>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>