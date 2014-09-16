<input type="hidden"
       id='<?= $this->renderID ?>'
       name='<?= $this->renderName ?>'
       value="<?= $this->value ?>"
       <?= $this->expandAttributes($this->options) ?>
       />