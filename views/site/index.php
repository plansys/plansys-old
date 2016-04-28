<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name;
?>
<center>
    <br/><br/><br/><br/><br/>
    <h1>Selamat Datang <i><?php echo CHtml::encode(Yii::app()->user->name); ?></i></h1>
    <br/>
    Posisi Anda adalah <b><?php echo User::itemAlias("roles", Yii::app()->user->roles); ?></b><br/><br/>
    ~ Untuk memulai silakan pilih menu di atas ~
</center>
