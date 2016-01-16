function snakeToCamel(s) {
    var cm = s.replace(/(\_\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    return cm.charAt(0).toUpperCase() + cm.slice(1);
}
if (!!window.opener.activeItem) {
    $scope.model.module = window.opener.activeItem.type;
}

$scope.updateTable = function () {
    $timeout(function () {
        $scope.model.modelName = snakeToCamel($scope.model.tableName.toLowerCase());
    });
}
$scope.changeConn = function() {
    $scope.tableList = {'Loading ...': 'Loading ...'};
    $scope.model.tableName = 'Loading ...';
    $scope.model.modelName = '';
    $http.get(Yii.app.createUrl('/dev/genModel/tableList', {'conn':$scope.model.conn}))
        .success(function(res) {
            $scope.tableList = res;
            $scope.model.tableName = '';
        });
}

$scope.tableList = $scope.params.tableList;

$scope.getListField = function() {
    $http.get(Yii.app.createUrl('/dev/genModel/fieldList', {'table':$scope.model.tableName}))
        .success(function(res) {
            $scope.fieldList = res;
        });
}

if ($scope.params.href != "") {
    window.close();
    window.opener.location.href = $scope.params.href;
}