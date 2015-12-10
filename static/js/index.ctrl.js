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
            require: ['ace/ext/emmet'],
            advanced: {
                enableEmmet: true,
            }
        }, options);
        return ret;
    }
    
    $timeout(function() {
        if ($scope.aceEditor) {
            $scope.aceEditor.focus();
        }
    });

    /************************** Plansys Widget *********************************/
    if (!$storage.widget) {
        $storage.widget = {};
    }

    $scope.$watch('$storage.widget.active', function () {
        $timeout(function () {
            $(window).resize();
        });
    });

    if ($("#widget-data").text() != "") {
        $storage.widget.list = JSON.parse($("#widget-data").text());
    }
    $scope.widget = {
        toggle: function (name) {
            if ($storage.widget.active == null) {
                $storage.widget.active = name;
            } else {
                if ($storage.widget.active == name) {
                    $storage.widget.active = '';
                } else {
                    $storage.widget.active = name;
                }
            }
            $timeout(function () {
                $(window).resize();
            }, 0);
        },
        isActive: function (name) {
            return ($storage.widget.active == name);
        }
    };

    $(window).resize(function () {
        var width = $("#widget-container").hasClass('ng-hide') ? 0 : $("#widget-container").width();
        $("#content").width($(window).width() - width);
    });
});