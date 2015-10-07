<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='70%' min-size="150px" class="sidebar">
            <div ui-header>
                <span>Directory</span>
                <div ng-click='browse(list.path)' class="btn btn-xs" style="margin-top:0px;">
                    <i class="fa fa-nm fa-refresh"></i>
                </div>
                <div ng-if='list.parent != ""' ng-click='browse(list.parent)' class="btn btn-xs btn-success" style="margin-top:0px;">
                    Up<i class="fa fa-nm fa-level-up"></i>
                </div>
            </div>
            <div ui-content oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list.item">
                        <li ng-repeat="item in list.item" ui-tree-node class='menu-list-item'>
                            <a href="#"
                               ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
                               <i class="fa {{item.type == 'dir' ? 'fa-folder' : 'fa-file'}} fa-nm"></i>
                               {{item.name}}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div style="overflow:hidden;border:0px;">
            <div ui-header>Properties</div>
            <div ui-content style="padding:3px 20px;">
                <div ng-include="Yii.app.createUrl('repoManager/renderProperties')" ng-hide="!isDir"></div>
                <div ng-show="!isDir">
                    <a href="{{Yii.app.createUrl('/repoManager/download',{
                                    'n' : file.name,
                                    'f' : file.path
                             })}}" class="btn btn-success btn-sm" >Download</a>
                    <div class="btn btn-danger btn-sm" ng-click="remove(file.path)">Remove</div>
                </div>
            </div>
        </div>
       
    </div>
</div>
<script type="text/javascript">
 app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
        $scope.list = <?= CJSON::encode($item);?>;
        //Decode Encode to Base64 start
        $scope._keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

        $scope.encode = function(input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;
            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                        $scope._keyStr.charAt(enc1) + $scope._keyStr.charAt(enc2) +
                        $scope._keyStr.charAt(enc3) + $scope._keyStr.charAt(enc4);

            }

            return output;
        };

        $scope.decode = function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = $scope._keyStr.indexOf(input.charAt(i++));
                enc2 = $scope._keyStr.indexOf(input.charAt(i++));
                enc3 = $scope._keyStr.indexOf(input.charAt(i++));
                enc4 = $scope._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            return output;

        };
        //Decode Encode end
        
        $scope.active = $scope.list;
        $scope.saving = false;
        $scope.isDir = true;
        $scope.file = null;
        $scope.remove = function(file){
            var request = $http({
                method: "post",
                url: Yii.app.createUrl('/repoManager/remove'),
                data: {file: file}
            }).success(
                function(html) {
                    $scope.file = null;
                    $scope.browse($scope.list.path);
                }
            );
        };
        $scope.select = function(item){
            $scope.file = null;
            $scope.activeTree = item;
            $scope.active = item.$modelValue;
            if($scope.active.type === 'dir'){
                $scope.isDir = true;
                $scope.browse($scope.active.path);
            }else{
                $scope.file = $scope.active;
                $scope.isDir = false;
            }
        };
        
  
        $scope.browse = function(item){
            var request = $http({
                'method':'post',
                'url':Yii.app.createUrl('/repoManager/browse'),
                'data':{
                        'path' : item
                       }
            }).success(function(data){
                $scope.list = data;
                $scope.active = data;
                $scope.isDir = true;
            });
        };
        
        $scope.is_selected = function(item) {
            if (item.$modelValue === $scope.active) {
                return "active";
            } else {
                return "";
            }
        };
        
        
     }
 ]);
</script>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

