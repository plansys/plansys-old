
app.controller("MainController", function ($scope, $http, $timeout, $localStorage) {
    $scope.$ = jQuery;
    $scope.size = Object.size;
    $scope.console = console;
    $scope.Yii = Yii;

    $storage = $localStorage;
    $scope.$storage = $storage;
    
    if (!$storage.widget) {
        $storage.widget = {};
    }
    

    $scope.widget = {
        toggle: function (widget) {
            if ($storage.widget.active == null) {
                $storage.widget.active = widget;
            } else {
                $storage.widget.active = null;
            }
            
            $timeout(function () {
                $(window).resize();
            }, 0);
        },
        isActive: function(widget) {
            return ($storage.widget.active == widget);
        }
    };

    $(window).resize(function () {
        $("#content").width($(window).width() - $("#widget-container").width());
    });
    $timeout(function () {
        $(window).resize();
    }, 0);
});