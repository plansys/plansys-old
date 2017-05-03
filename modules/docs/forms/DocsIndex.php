<?php

class DocsIndex extends Form {

    public function getForm() {
        return array (
            'title' => 'Daftar ',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'index.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<style>
    .docs-left {
        position:absolute;
        left:0px;
        bottom:0px;
        top:0px;
        width:250px;
        overflow-y:auto;
        border-right:1px solid #ccc;
    }
    .docs-right {
        position:absolute;
        left:250px;
        bottom:0px;
        top:0px;
        right:0px;
        display:flex;
        flex-direction: column;
        transition: .2s;
    }
    
    @-webkit-keyframes slide {
        100% { left: 0; }
    }
    
    @keyframes slide {
        100% { left: 0; }
    }
    .docs.nomenu .docs-left {
        display:none;
    }
    
    .docs.nomenu .docs-right {
        left:0px;
    }
    
    .show-menu .desktop {
        display:inline;
    }
    
    .show-menu .mobile {
        display:none;
    }
    
    @media only screen and (max-width: 768px) {
        .docs  .docs-left {
            display:block !important;
            right:0px;
            width:auto;
        }
        .docs .docs-right{
            display:none !important;
        }
        
        .docs.mobileselect .docs-left {
            display:none !important;
            right:0px;
            width:auto;
        }
        
        .docs.mobileselect .docs-right {
            left:0px;
            display:block !important;
        }
    
        .show-menu .desktop {
            display:none;
        }
        
        .show-menu .mobile {
            display:inline;
        }
    }
    
    .docs-header {
        flex: 0 0 35px;
        border-bottom:1px solid #ccc;
        display:flex;
        flex-direction:row;
        background:#fff;
        align-items:stretch;
        justify-content: space-between;
    }
    .docs-btn {
        border-right:1px solid #ccc;
        border-left:1px solid #ccc;
        flex:0 0 40px;
        display:flex;
        justify-content:center;
        align-items:center;
        color:black;
        cursor:pointer;
        background:#fff;
        margin-left:0;
    }
    .docs-btn:first-child {
        border-left:0px;
    }
    .docs-btn.right {
        margin-left:auto;
        margin-right:0;;
        border-right:0px;
    }
    .docs-btn:hover {
        background:#ececeb;
    }
    .docs-content {
        flex: 1;
        overflow:auto;
    }
</style>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"docs\" ng-class=\"{
    nomenu:!menu,
    mobileselect:mobileselect
}\">
    <div class=\"docs-left\">',
            ),
            array (
                'type' => 'TreeView',
                'name' => 'tree',
                'options' => array (
                    'ng-change' => 'changed(model.tree)',
                ),
                'data' => '',
                'initFunc' => 'Docs::browse()',
                'expandFunc' => 'Docs::browse($item)',
            ),
            array (
                'type' => 'Text',
                'value' => ' </div>',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '    <div class=\"docs-right\">
        <div class=\"docs-header\">
            <div class=\"show-menu docs-btn\" ng-click=\"toggleMenu()\">
                <i ng-if=\"menu\" class=\"desktop fa fa-chevron-left\"></i>
                <i ng-if=\"!menu\" class=\"desktop fa fa-chevron-right\"></i>
                
                <i class=\"mobile fa fa-bars\"></i>
            </div>
            
            <div class=\"docs-btn right\">
                <i class=\"fa fa-pencil\"></i>
            </div>
            <div class=\"docs-btn\">
                <i class=\"fa fa-plus\"></i>
            </div>
        </div>
        <div class=\"docs-content\">
            
        </div>
    </div>
</div>',
            ),
        );
    }

}