<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle = Yii::app()->name . ' - Error';
?>
<div ui-layout>
    <div style="padding:100px;text-align:center;">
        <h2>Error <?php echo $code; ?></h2>

        <div class="error">
            <?php echo CHtml::encode($message); ?>
        </div>
    </div>
</div>