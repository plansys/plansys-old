
app.controller("MainController", ["$scope", "$http", function($scope, $http) {
        $scope.$ = jQuery;
        $scope.size = Object.size;
        $scope.console = console;
        $scope.Yii = Yii;
    }
]);