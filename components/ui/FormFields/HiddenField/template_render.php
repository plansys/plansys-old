<input type="hidden"
       id='<?= $this->renderID ?>'
       name='<?= $this->renderName ?>'
       value="<?= $this->value ?>"
       <?php
       if (!isset($this->options['ng-value'])) {
           $this->options['ng-value'] = "model." . $this->name;
       }

       if (!isset($this->options['ng-model'])) {
           $this->options['ng-model'] = "model." . $this->name;
       }

       echo $this->expandAttributes($this->options)
       ?>
       />