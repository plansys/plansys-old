<?php $this->mode->registerJS('code', 'ace-min.emmet-min'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ace-min'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ext-emmet'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ext-modelist'); ?>
<?php $this->mode->registerJS('code', 'code'); ?>
<style>
<?php include("style.css"); ?>
</style>
<div ng-controller="Code" id='code-editor-container'>
<?php include("toolbar.php"); ?>
</div>