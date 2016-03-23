<div sql-criteria <?= $this->expandAttributes($this->options) ?>>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="base_class" class="hide"><?= $this->baseClass; ?></data>
    <data name="params_field" class="hide"><?= $this->paramsField; ?></data>
    <pre name="inline_js" class="hide"><?= $this->inlineJS; ?></pre>

    <div class="properties-header">
        <div ng-click="CriteriaDialog.open()" 
             style="margin:-3px -3px 0px 0px;" class="btn btn-xs pull-right btn-default">
            <i class="fa fa-pencil"></i> Edit SQL
        </div>

        <span ng-if="isError" 
              tooltip="{{errorMsg}}" tooltip-append-to-body='true'
              style="float:right;margin-right:10px;font-size:11px;color:red;">
            <i class="fa fa-warning"></i> ERROR
        </span>
        <div> 
            <?= $this->label; ?> 

        </div>
    </div>
    <table style="width:100%;margin-right:-1px;">
        <tr>
            <td style="padding:3px;border-right:0px;">
                
                <center ng-show='isLoading' style='padding:10px;color:#999;font-size:12px;'>
                    <i class="fa fa-link"></i> Loading SQL
                </center>
                
                <div ng-show='!isLoading' ng-bind-html="previewSQL" style="
                    margin-bottom:-10px;
                    -moz-user-select:text;
                    -webkit-user-select:text;
                    user-select:text;
                "></div>
            </td>
        </tr>
    </table>

    <?php
    echo FormBuilder::build('ModalDialog', [
        'name' => 'CriteriaDialog',
        'subForm' => 'SqlCriteriaForm',
    ]);
    ?>

</div>
