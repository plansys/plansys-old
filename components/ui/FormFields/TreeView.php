<?php

class TreeView extends FormField {
    
    public $type = 'TreeView';
    public $name = '';
    public $options = [];
    public static $toolbarName = "Tree View";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-sitemap";
    
    public $data = [];
    public $debug = "OFF";
    public $initFunc = "";
    public $expandFunc = "";
    public $itemMap = [
        "id" => "id",
        "parentid" => "parentid",
        "canExpand" => "canExpand",
        "label" => "label",
        "icon"  => "icon",
        "items" => "items",
    ];
    
    public $itemOptions = [];

    public function render() {
        $this->data = $this->evaluate($this->initFunc, true);
        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);
        return $this->renderInternal('template_render.php');
    }

    public function includeCSS() {
        return ['treeview.css'];
    }

    public function includeJS() {
        return ['treeview.js'];
    }
    
    public function actionExpand() {
        $input = file_get_contents("php://input");
        $post = json_decode($input, true);
        
        $name = $post['n'];
        $result = [];
        $fb    = FormBuilder::load($post['c']);
        if ($fb) {
            $field = $fb->findField(['name' => $name]);
            if ($field) {
                $result = $this->evaluate($field['expandFunc'], true, $post);
            }
        }
        
        $result = is_array($result) ? $result : [];
        echo json_encode($result);
    }
    
    public function getFieldProperties() {
        return array (
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<style>
    .treeview-itemap {
        display:block;
        width:100%;
        line-height:25px;
        padding:0px 5px;
    }
</style>',
            ),
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left',
                ),
                'type' => 'DropDownList',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
            ),
            array (
                'label' => 'Debug Tree Data',
                'name' => 'debug',
                'fieldWidth' => '5',
                'options' => array (
                    'ng-model' => 'active.debug',
                    'ng-change' => 'save();',
                ),
                'labelOptions' => array (
                    'style' => 'font-size: 12px;
text-align:left;
margin-top: -4px;',
                ),
                'size' => 'small',
                'type' => 'ToggleSwitch',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Init Function',
                'fieldname' => 'initFunc',
                'desc' => 'This function will be executed on tree init',
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Expand Function',
                'fieldname' => 'expandFunc',
                'desc' => 'This function will be executed when a tree item is expanded<br/>
<hr/>
Use <code>$item</code> as parameters to get the expanded item. <br/>
This function should return array of child item',
                'type' => 'ExpressionField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Item Options',
                'name' => 'itemOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Tree Item Map',
                'type' => 'SectionHeader',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<div style=\"margin-top:-25px\"
    ng-click=\"showItemMap = !showItemMap\"
    class=\"btn btn-xs btn-success pull-right\">
    <i class=\"fa fa-pencil\"></i> Edit Item Map
</div>
<table style=\"margin:5px 0px;\" ng-show=\"showItemMap\" class=\"table table-bordered table-condensed\">
    <tr ng-repeat=\"(k, v) in active.itemMap\">
        <td>{{ k }}</td>
        <td style=\"padding:0px\">
            <input class=\"treeview-itemap\" type=\"text\" ng-model=\"v\">
        </td>
    </tr>
</table>
<div class=\"clearfix\"></div>',
            ),
        );
    }


}