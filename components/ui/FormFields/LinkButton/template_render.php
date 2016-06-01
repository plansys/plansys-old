<a <?= $this->expandAttributes($this->options) ?>>

    <i class="fa fa-<?= $this->icon ?>" 
       style="margin-right:4px;<?php if ($this->icon == '') { ?>display:none; <?php } ?>"></i>
    <?= $this->label ?>
</a>