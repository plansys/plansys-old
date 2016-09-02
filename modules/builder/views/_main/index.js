/* global $scope, $http, $timeout, app, builder */

app.controller("Index", function ($scope, $http, $timeout) {
    builder.ng.$http = $http;
    builder.ng.indexScope = $scope;

    $scope.col1 = {collapsed: false, resizable: false, width: '20%'};
    $scope.col2 = {collapsed: false, resizable: false, width: 'auto'};
    $scope.col3 = {collapsed: false, resizable: false, width: '25%'};

    $scope.builder = builder;
    $scope.$watch('builder.active.$meta', function (n, o) {
        if (n.columns.length < 3) {
            $scope.col3.collapsed = true;
        } else {
            $scope.col3.collapsed = false;
        }
    });

    builder.activate('code'); // initialize builder
});
app.controller("FirstCol", function ($scope, $http, $timeout, $ocLazyLoad) {});
app.controller("SecondCol", function ($scope, $http, $timeout) {});
app.controller("ThirdCol", function ($scope, $http, $timeout) {});