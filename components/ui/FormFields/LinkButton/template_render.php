<a link-btn group="<?= $this->group ?>"
      id="<?= $this->renderID ?>"
      class="link-btn 
          <?php if ($this->buttonType != 'not-btn'): ?>btn btn-<?= $this->buttonType ?> <?= $this->buttonSize ?><?php endif; ?>" 
      <?= $this->expandAttributes($this->options) ?>>

    <i class="fa fa-<?= $this->icon ?>" 
       style="margin-right:4px;<?php if ($this->icon == '') { ?>display:none; <?php } ?>"></i>
    <?= $this->label ?>
</a>