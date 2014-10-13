<div sql-criteria <?= $this->expandAttributes($this->options) ?>>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="value" class="hide"><?= json_encode($this->value); ?></data>
    <pre name="inline_js" class="hide"><?= $this->inlineJS; ?></pre>
    
    <div class="properties-header">
        <div ng-click="CriteriaDialog.open()" 
             style="margin:-3px -3px 0px 0px;" class="btn btn-xs pull-right btn-default">
            <i class="fa fa-pencil"></i> Edit SQL
        </div>
        <div> 
            <?= $this->label; ?>
        </div>
    </div>
    <table style="width:100%;margin-right:-1px;">
        <tr>
            <td style="padding:3px;border-right:0px;">
                <pre style="font-size:12px;margin:0px;padding:5px 8px;white-space:pre-wrap;">{{ previewSQL}}</pre>
            </td>
        </tr>
    </table>

    <?php
    echo FormBuilder::build('ModalDialog', array(
        'name' => 'CriteriaDialog',
        'subForm' => 'SqlCriteriaForm',
    ));
    ?>

</div>
