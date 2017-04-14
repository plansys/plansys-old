<?php

class HelpWelcome extends Form {

    public function getForm() {
        return array (
            'title' => 'Plansys - Welcome',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'welcome.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<style>
    
    .welcome-page {
        text-align: center;
        padding: 15% 25% 15% 25%;
    }
    
    .welcome-page h1 {
        font-size: 135px;
        font-weight: 600;
        vertical-align: middle;
        background-color: #37474F;
        color: transparent;
        text-shadow: 0px 2px 5px rgba(255,255,255,.3);
        -webkit-background-clip: text;
        -moz-background-clip: text;
        background-clip: text;
        margin-bottom: 0px;
    }
    
    @media screen and (max-width: 768px) {
        .welcome-page h1 {
            font-size: 70px
        }
        
        .welcome-page {
            text-align: center;
            padding: 15% 10% 15% 10%;
        }
        
    }
</style>',
            ),
            array (
                'renderInEditor' => 'Yes',
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<div class=\"welcome-page\">
    <h1>Plansys</h1>    
    <span>Licensed Under GPL v.3</span>
    <hr>
    <div class=\"row\" style=\"margin-top: 10px;\">
        <div class=\"col-sm-12 col-md-3\">
            CPU : {{cpu}}%
        </div>
        <div class=\"col-sm-12 col-md-3\">
            RAM : {{mem}}%
        </div>
        <div class=\"col-sm-12 col-md-3\">
            HDD : {{hdd}}
        </div>
        <div class=\"col-sm-12 col-md-3\">
            PHP : {{php}}
        </div>
    </div>
</div>
',
            ),
        );
    }

}