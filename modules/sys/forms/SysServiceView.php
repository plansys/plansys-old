<?php

class SysServiceView extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Service ',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'SysServiceView.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div class=\"panel panel-default\"
    style=\"width:500px;margin:40px auto;\">
    <div class=\"panel-body\">
       <div ng-if=\"!!params.svc && params.svc == \'finished\'\" style=\"padding:30px 20px;color:green;text-align:center;\">
            <i 
            class=\"fa fa-check-circle fa-4x\">
            </i>
            <br/>
            <h4 style=\"
            margin:10px 0px 0px 0px;
            line-height:25px;
            \">
                Process Finished
            </h4>
        </div>
        <div ng-if=\"!!params.svc && !!params.svc.view.failed\" style=\"padding:30px 20px;text-align:center;color:rgba(233, 79, 47,1)\">
            <i 
            class=\"fa fa-warning fa-4x\">
            </i>
            <br/>
            <h4 style=\"
            margin:10px 0px 0px 0px;
            line-height:25px;
            \">
                {{ params.svc.view.failed }}
            </h4>
            <div ng-bind-html=\"params.svc.view.body\"></div>
        </div>
       <div ng-if=\"!!params.svc && params.svc != \'finished\' && !params.svc.view.failed\">
            <div ng-bind-html=\"params.svc.view.title\"></div>
            <div class=\"progress\" ng-if=\"params.svc.view.progress >= 1\" style=\"margin-top: 10px;\">
             <div class=\"progress-bar progress-bar-success\"
                  ng-class=\"{
                      \'progress-bar-striped\': params.svc.view.progress < 100,
                      \'active\': params.svc.view.progress < 100
                  }\"
                  role=\"progressbar\" 
                  aria-valuenow=\"{{params.svc.view.progress}}\" 
                  aria-valuemin=\"0\" 
                  aria-valuemax=\"100\"
                  style=\"width: {{params.svc.view.progress}}%;min-width:0px;\">
               <span class=\"sr-only\">{{params.svc.view.progress || 0}}% Complete</span>
             </div>
            </div>
            <div ng-bind-html=\"params.svc.view.body\"></div>
       </div>
       <div ng-if=\"!params.svc\" class=\"text-center\">
           <i class=\"fa fa-refresh fa-spin\"></i> Loading<span style=\'width:20px;display:inline-block;text-align:left;\'>{{ poolDots }}</span>
           </span> 
       </div>
    </div>
</div>',
            ),
        );
    }

}