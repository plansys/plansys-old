<?php $this->mode->registerJS('code', 'ace-min.emmet-min'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ace-min'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ext-emmet'); ?>
<?php $this->mode->registerJS('code', 'ace-min.ext-modelist'); ?>
<?php $this->mode->registerJS('code', 'code'); ?>
<style>
<?php include("style.css"); ?>
</style>
<?php include("toolbar.php"); ?>
<div id="code-editor" ng-controller="Code" style="
    position: absolute;
    top: 27px;
    right: 0;
    bottom: 0;
    left: 0;
"></div>
