<?php
FormField::$inEditor = false;
?>

<div class = "properties-header"><i class = "fa fa-file-text"></i>&nbsp;
    Form Properties</div>

<div ui-content style="padding:6px 5px 0px 10px;">
    <?php
    $fp = FormBuilder::load('DevFormProperties');
    echo $fp->render($fb->form);
    ?>
</div>
<?php
FormField::$inEditor = true;
?>