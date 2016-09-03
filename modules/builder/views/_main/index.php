<?php Yii::import('application.components.utility.Asset'); ?>
<div ng-controller="Index">
    <div id="builder">
        <div ui-layout options="{ flow : 'column',dividerSize:1,disableToggle:true}">
            <div id="1st-col" 
                 ui-layout-container 
                 size="{{col1.width}}" 
                 resizable="col1.resizeable"
                 collapsed="col1.collapsed" 
                 class="sidebar">
                <div ng-controller="FirstCol"
                     ng-show="!col1.view.loading"
                     ng-include 
                     src="col1.view.url"
                     onload="builder.activated(1)"></div>

                <div ng-if="col1.view.loading" class="text-center">
                    <i class="fa fa-refresh fa-spin fa-3x"></i>
                </div>

                <hr>
                Choose Tree:<br/>
                <div class="btn btn-default btn-xs" ng-click="builder.activate('code')">Code</div>
                <div class="btn btn-default btn-xs" ng-click="builder.activate('form')">Form</div>
                <div class="btn btn-default btn-xs" ng-click="builder.activate('model')">Model</div>

            </div>
            <div id="2nd-col"
                 ui-layout-container
                 size="{{col2.width}}" 
                 resizable="col2.resizeable"
                 collapsed="col2.collapsed">

                <pre>{{ col2 | json }}</pre>
                {{ col2.view.loading}}

                <div ng-controller="SecondCol" 
                     ng-include
                     src="col2.view.url"
                     onload="builder.activated(2)"></div> 
            </div>
            <div id="3rd-col" 
                 ui-layout-container 
                 size="{{col3.width}}" 
                 resizable="col3.resizeable"
                 collapsed="col3.collapsed">

                <pre>{{ col3 | json }}</pre>

                {{ col3.view.loading}}

                <div ng-controller="ThirdCol" 
                     ng-include 
                     src="col3.view.url" 
                     onload="builder.activated(3)"></div>
            </div>
        </div>
    </div>
</div>