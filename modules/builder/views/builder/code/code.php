<?php Asset::registerJS($this->vpath . '.code.ace-min.emmet'); ?>
<?php Asset::registerJS($this->vpath . '.code.ace-min.ace'); ?>
<?php Asset::registerJS($this->vpath . '.code.ace-min.ext-emmet'); ?>
<?php Asset::registerJS($this->vpath . '.code.code'); ?>
<div id="code-editor" ng-controller="Code" style="
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
"></div>
