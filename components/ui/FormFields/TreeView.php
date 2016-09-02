<?php

class TreeView extends FormField {

    public $initMode = 'url';
    public $initUrl  = '';
    public $initFunc = '';
    public $expandMode = 'url';
    public $expandUrl  = '';
    public $expandFunc = '';
    public $options = [];

    public function includeJS() {
        return ['treeview.js'];
    }

    public function actionTemplate() {
        echo "<div>haduh {{ \$ctrl | json }}</div>";
    }

    public function actionRender() {
        echo $this->render();
    }
    
    public function render() {
        return parent::render();
    }

}
