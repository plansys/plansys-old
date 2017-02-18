/* global $scope, $http, $timeout, app, builder */

app.controller("Index", function ($scope, $http, $timeout) {
    $scope.layout = {
        col1: {
            width: '17%',
            minWidth: '150px'
        },
        col2: {
            width: 'auto'
        }
    };
    window.builder = $scope;
    $timeout(function() {
        $scope.tabs = window.tabs;
        $scope.$watch('tabs.active', function(i) {
            $scope.active = !!$scope.tabs.active;
        })
    });
});
