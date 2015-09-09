<?php

class InstallModule extends CWebModule {

    public function accessControl($controller,$action) {
        
    }

    public function init() {
        // import the module-level controllers and forms
        $this->setImport(array(
            'application.modules.install.controllers.*',
            'application.modules.install.forms.*'
        ));
    }

}