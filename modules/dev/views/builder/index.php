<?php Yii::import('application.components.utility.Asset'); ?>
<?php 
Asset::registerJS('application.modules.dev.views.builder.index'); 
$dirs = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR  . "*");
foreach($dirs as $dir) {
    $initFile = $dir . DIRECTORY_SEPARATOR . "init.js";
    if (is_file($initFile)) {
        Asset::registerJS(Helper::getAlias($initFile));
    }
}
?>
<div ng-controller="IndexController">
    <div id="builder">
        <div ui-layout options="{ flow : 'column',dividerSize:1,disableToggle:false}">
            <div id="tree" ui-layout-container size='20%'S min-size="200px" class="sidebar">
                <div ng-include="builder.active.tree.url"></div>
                 <div class="btn btn-default" ng-click="builder.activate('form')">Form</div>
                 <div class="btn btn-default" ng-click="builder.activate('model')">Model</div>
            </div>
            <div id="editor" ui-layout-container>
                <div ng-include="builder.active.editor.url"></div> 
            </div>
            <div id="properties" ui-layout-container size="30%">
                <div ng-include="builder.active.properties.url"></div>
            </div>
        </div>
    </div>
</div>
<script>
    app.controller("IndexController", ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
        builder.ng.$http = $http;
        builder.activate('form');
        $scope.builder = builder;
    }]);
</script>
