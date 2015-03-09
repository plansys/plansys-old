<?php

class InstallModule extends CWebModule {

    public function init() {
        // import the module-level models and components
        $this->setImport(array(
            'application.models.*',
            'application.modules.install.controllers.*',
            'application.modules.install.forms.*',
        ));
    }
}