
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

    $scope.$watch('$storage.widget.active', function () {
        $timeout(function () {
            $(window).resize();
        }, 0);
    });

    if ($("#widget-data").text() != "") {
        $storage.widget.list = JSON.parse($("#widget-data").text());
    }

    $scope.widget = {
        toggle: function (name) {
            if ($storage.widget.active == null) {
                $storage.widget.active = name;
            } else {
                $storage.widget.active = null;
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
        $("#content").width($(window).width() - $("#widget-container").width());
    });
    $timeout(function () {
        $(window).resize();
    }, 0);
});