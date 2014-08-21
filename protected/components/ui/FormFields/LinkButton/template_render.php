<span link-btn group="<?= $this->group ?>"
      url="<?= $this->createUrl($this->url); ?>"

      class="link-btn btn btn-<?= $this->buttonType ?> <?= $this->buttonSize ?>" 
      <?= $this->expandAttributes($this->options) ?>>

    <data name="urlparams" class="hide" ><?php
        echo "{";
        $i = 1;
        foreach ($this->urlparams as $k => $p) {
            echo "\"$k\":$p" . ($i != count($this->urlparams) ? ',' : '');
            $i++;
        }
        echo "}";
        ?></data>
    <i class="fa fa-<?= $this->icon ?>" style="<?php if ($this->icon == '') { ?>display:none; <?php } ?>"></i>
    <?= $this->label ?>
</span>