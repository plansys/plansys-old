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
    
    $scope.canMenuScrollRight = true;
    
    $scope.menuScrollRight = function() {
        $(".header .scroll").animate({
            scrollLeft: $(".header .scroll").scrollLeft() + $(".header .scroll").width()
        }, 200).promise().done(function () {
            $scope.canMenuScrollRight = false;
        });
    }
    
    $(".header .scroll").on("touchend click touchmove", function(e) {
        $timeout(function() {
            $scope.canMenuScrollRight = false;
        }.bind(this));
        
        if (!$(e.target).is("a")) {
            $(".header .top-menu").removeClass("open");
            $(".nav .dropdown.open").removeClass("open");
        }
    })
    
    $(".header .navbar-nav .dropdown > a").on("click", function(e) {
        $t = $(this);
        
        if ($t.parent().find("> .dropdown-menu").length > 0) {
            
            if ($t.parent().parent().hasClass("navbar-nav")) {
                $(".header .top-menu").addClass("open");
                $(".nav .dropdown.open").removeClass("open");
                $t.parent().addClass("open");
            }
            else {
                $t.parent().parent().find(".open").removeClass('open');
                $t.parent().addClass('open');    
            }
            
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    })
    
    $timeout(function() {
        $(window).resize();
    });
});