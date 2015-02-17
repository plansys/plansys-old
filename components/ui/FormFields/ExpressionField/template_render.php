<div expression-field <?= $this->expandAttributes($this->options) ?>>
    <!-- data -->
    <data name="value" class="hide"><?= $this->value ?></data>
    <data name="field_name" class="hide"><?= $this->fieldname ?></data>
    <data name="field_language" class="hide"><?= $this->language ?></data>
    <data name="model_class" class="hide"><?= @get_class($model) ?></data>
    <!-- /data -->
    <div class="properties-header" style="cursor:default;">
        <div class="label label-default" style="font-size:10px;float:right;margin:-1px 3px -3px 0px;">
            {{lang | uppercase}}
        </div>
        <?= $this->label ?>
    </div>

    <div ng-show="lang == 'html' || isFocus || value.trim() != ''">
        <table>
            <tbody>
                <tr valign="top">
                    <td style="padding:0px;background:#333;color:white;border:0px;">
                        <textarea spellcheck="false"
                                  class="textarea-noresize"
                                  ng-model="value"
                                  ng-change="validate()"
                                  ng-delay="500"
                                  ng-focus="focus()"
                                  ng-blur="blur()"
                                  auto-grow
                                  id="<?= $this->renderID ?>"
                                  name="<?= (isset($this->options['name']) ? $this->options['name'] : $this->renderName) ?>"
                                  style='border:0px;background:#333;height:22px;'>
                                      <?= $this->value ?>
                        </textarea>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="field-box-desc" style="text-align:center;" 
         ng-show="lang != 'html' && value.trim() == '' && !isFocus">
        <div class="btn btn-xs btn-default" style="border-radius:0px;"
             ng-click="forceFocus();">
            Create Expression
            <i class="fa fa-arrow-right"></i>
        </div>
    </div>

    <?php if ($this->desc != ''): ?>
        <div ng-show="value.trim() == '' && !isFocus" class="field-box-desc">
            <?= $this->desc ?>
        </div>
    <?php endif; ?>

    <div class="clearfix"></div>
</div>