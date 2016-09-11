<?php

class TreeView extends FormField {

    public $initMode      = 'url';
    public $initUrl       = '';
    public $initFunc      = '';
    public $expandMode    = 'url';
    public $expandUrl     = '';
    public $expandFunc    = '';
    public $itemLayoutUrl = '';
    public $options       = [];
    public static $deprecated = true;
    
    public function includeJS() {
        return ['treeview.js'];
    }

    public function actionTemplate() {
        echo $this->renderInternal("template_component.php");
    }

    public function actionItemLayout() {
        echo $this->renderInternal("template_item_layout.php");
    }

    public function render() {
        if (!isset($this->options['data'])) {
            $this->options['data'] = [];
        }

        if ($this->itemLayoutUrl == '') {
            $this->itemLayoutUrl = Yii::app()->createUrl('formfield/TreeView.itemLayout');
        }
        
        ## everything that will be read by treeview component
        ## should be stored in $this->options['data'] 
        
        $this->options['data']['itemLayoutUrl'] = $this->itemLayoutUrl;
        if ($this->initMode == 'url') {
            $this->options['data']['initUrl'] = $this->initUrl;
        } else {
            $this->options['data']['initData'] = $this->initFunc;
        }

        $this->options['data'] = str_replace("\"", "'", json_encode($this->options['data']));
        return parent::render();
    }

}
