app.controller("MainController", function ($scope, $http, $timeout, $localStorage) {
    $scope.$ = jQuery;
    $scope.size = Object.size;
    $scope.console = console;
    $scope.Yii = Yii;
    $scope.title = $("title").text();
    $storage = $localStorage;
    $scope.$storage = $storage;

    /*********************** JS Helper Function ********************************/
    $scope.typeof = function (val) {
        return typeof val;
    }
    $scope.objectSize = function (val) {
        if (val == null || typeof val != 'object') {
            return 0;
        } else {
            return Object.keys(val).length;
        }
    }
    $scope.isArray = function (val) {
        return Object.prototype.toString.call(val) === '[object Array]';
    }
    $scope.getParent = function (s) {
        var parent = s.$parent;
        while (!parent.hasOwnProperty('modelBaseClass') && !!parent.$parent) {
            parent = parent.$parent;
        }
        return parent;
    }

    /************************ ACE Editor Config ********************************/
    $scope.aceConfig = function (options) {
        var ret = $.extend({
            useWrapMode: true,
            showGutter: true,
            onLoad: function(ed) {
                $scope.aceEditor = ed;
            },
            theme: 'monokai',
            mode: 'php',
            require: ['ace/ext/emmet', 'ace/ext/language_tools'],
            advanced: {
                enableEmmet: true,
                // enableBasicAutocompletion: true,
                // enableSnippets: true,
                enableLiveAutocompletion: true,
                liveAutocompletionDelay: 100,
                liveAutocompletionThreshold: 3
            }
        }, options);
        return ret;
    }
    
    $timeout(function() {
        if ($scope.aceEditor) {
            $scope.aceEditor.focus();
        }
        $(window).resize();
    });
});