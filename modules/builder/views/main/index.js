/* global $scope, $http, $timeout, app, builder */

app.controller("Index", function ($scope, $http, $timeout) {
    $scope.builder = builder;
    $scope.col1 = {
        layout: {
            collapsed: false,
            resizable: false,
            width: '17%',
            minWidth: '250px'
        },
        ctrl: null,
        view: null
    };

    $scope.col2 = {collapsed: false, resizable: false, width: 'auto'};
    $scope.col3 = {collapsed: false, resizable: false, width: '25%'}; // kalau ada minWidth ngebug

});

app.controller("Col1", function ($scope, $http) {
    $scope.$parent.col1.ctrl = $scope;
    $scope.layout = $scope.$parent.layout;
    $scope.view = $scope.$parent.view;

    $scope.activeTabClass = function (view) {
        return $scope.view.name === view ? 'active' : '';
    };

    $scope.showTabBtn = function (view) {
        var activeName = $scope.view.name;
        return (activeName === view && !$scope.col1.view.loading) || activeName !== view;
    };

    $scope.isLoading = function (view) {
        if ($scope.view.name === view && $scope.col1.view.loading) {
            return true;
        }
        return false;
    };

    $scope.activate = function (view) {
        var colName = builder.views[view].$meta.columns[0];
        $scope.view = builder.views[view][colName];
        $scope.view.name = view;

        if (typeof $scope.view.init === 'function') {
            $scope.view.init();
        }
        $scope.view.loading = false;
    };

    $scope.activated = function () {
        $scope.view.activated = true;
        $scope.view.loading = false; // stop loading when column is loaded
    };


    $scope.activate('form');

});