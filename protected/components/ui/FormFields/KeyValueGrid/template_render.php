<div key-value-grid class='field-box keyvalue' <?= $this->expandAttributes($this->options) ?>>
    <!-- data -->
    <data name="field_name" class="hide"><?= $this->fieldname ?></data>
    <data name="field_show" class="hide"><?= $this->show ?></data>
    <data name="allow_space" class="hide"><?= $this->allowSpaceOnKey ?></data>
    <data name="allow_empty" class="hide"><?= $this->allowSpaceOnKey ?></data>
    <data name="allow_dquote" class="hide"><?= $this->allowDoubleQuote ?></data>
    <data name="model_class" class="hide"><?= @get_class($model) ?></data>
    <data name="value" class="hide"><?= json_encode($this->value) ?></data>
    <!-- /data -->

    <div class="properties-header" >
        <div class="editjson"
             ng-class="mode" ng-click="show = true;
                     mode = (mode == 'grid' ? 'json' : 'grid');
                     json = json.trim() + ' '">
            <i class="fa fa-pencil"></i>
        </div>
        <div style="width:96%" ng-click="show = !show">
            <div style="padding:2px 5px 2px 5px;">
                <div ng-show="mode == 'json'" class="label label-default" 
                     style="font-size:10px;float:right;margin:3px 6px -3px 0px;">
                    JSON
                </div>
                <div ng-show="mode == 'grid'" class="pull-right" style="margin-right:5px;">
                    <i class="fa  fa-toggle-{{!show ? 'right' : 'up' }}"></i>
                    {{!show ? 'Show' :'Hide' }}
                </div>

                <i class="fa fa-columns"></i>
                <?= $this->label ?>
            </div>
        </div>
    </div>
    <div ng-hide='!show || mode != "grid"'>
        <table>
            <tbody>
                <tr valign="top" ng-repeat="v in value track by $index">
                    <td style='width:40%'>
                        <input style='border:0px;'
                               type="text"
                               ng-model="v.key"
                               ng-change="change()"
                               ng-delay="500"/>
                    </td>
                    <td>
                        <textarea auto-grow spellcheck="false" class="textarea-noresize"
                                  style='border:0px;height:22px;' ng-model="v.value" ng-change="change()"
                                  ng-delay="500"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div ng-hide='!show || mode != "json"' style="margin-top:-1px;">
        <table>
            <tbody>
            <td style="padding:0px;background:#333;color:white;border:0px;">
                <textarea spellcheck="false" name="<?= $this->renderName ?>" class="textarea-noresize"
                          ng-model="json" ng-change="changeJSON()" ng-delay="500"
                          style='border:0px;background:#333;height:22px;' auto-grow></textarea>
            </td>
            </tbody>
        </table>
        <div ng-show="json_error != ''"
             style="color:red !important;background:#333;font-size:12px;border-top:1px solid #666;">
            &nbsp; <i class="fa fa-warning"></i>
            Invalid JSON
        </div>
    </div>
    <div class="clearfix"></div>
</div>
