<?php
/* @var $this SiteController */
/* @var $error array */

?>
<div ui-layout>
    <div style="padding:100px;text-align:center;">
        <div class="">
            <div style='margin-bottom:10px;'>
                <i class="fa fa-warning fa-4x"></i> 
                <div style='font-size:20px;'><?= $code; ?></div>
            </div>
            <?php echo $message; ?>
            <br/><br/>
            <a onclick="window.history.back();" class="btn-default btn">
                <i class="fa fa-arrow-left"></i> <b>Kembali</b>
            </a>
        </div>
    </div>
</div>