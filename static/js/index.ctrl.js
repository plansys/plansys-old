
app.controller("MainController", function ($scope, $http, $timeout, $localStorage) {
    $scope.$ = jQuery;
    $scope.size = Object.size;
    $scope.console = console;
    $scope.Yii = Yii;
    $scope.title = $("title").text();

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
    $timeout(function () {
        $(window).resize();
    }, 0);
});