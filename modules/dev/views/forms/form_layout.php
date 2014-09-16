<?php
FormField::$inEditor = false;
?>

<div class="properties-header">
    <div class='btn btn-default btn-xs pull-right '
         ng-click='unselectLayout();'>
        <i class='fa fa-times'></i>
        Close
    </div>
    <i class = "fa fa-file-text"></i>&nbsp;
    Form Layout <span class="label label-default">{{layout.name| uppercase }}</span>
</div>

<div ui-content style="padding:6px 5px 0px 10px;">
    <?php
    $fpl = FormBuilder::load('DevFormLayoutProperties', array(
        'module' => $fb->module
    ));
    echo $fpl->render();
    ?>
</div>
<?php
FormField::$inEditor = true;
?>