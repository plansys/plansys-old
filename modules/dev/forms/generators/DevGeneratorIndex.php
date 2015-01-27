<?php

class DevGeneratorIndex extends Form {
    
    public function getForm() {
        return array (
            'title' => 'Plansys Form Generator',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'value' => '<div class=\"col-sm-2\"></div>
<div class=\"col-sm-8\" style=\"padding-top:40px;\">
    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
          <i class=\"fa fa-lg fa-trello\" style=\"margin:0px 5px 0px -5px;\"></i>
          Choose form template
      </div> 
      <div class=\"panel-body\">
            <div class=\"row\">
                <div ng-repeat=\"t in params.templates\"  
                    class=\"col-xs-6 col-md-3\">
                    <a href=\"#\" class=\"thumbnail\">
                        <img src=\"{{t.icon}}\" alt=\"{{t.name}}\">
                    </a>
                </div>
            </div>
      </div>
    </div>
</div>
<div class=\"col-sm-2\"></div>',
                'type' => 'Text',
            ),
        );
    }

}