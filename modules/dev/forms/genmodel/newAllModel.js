function snakeToCamel(s) {
    s = s.toLowerCase();
    var cm = s.replace(/(\_\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    return cm.charAt(0).toUpperCase() + cm.slice(1);
}

$scope.mode = 'standby';
$scope.btnTitle = "Please choose model to generate";
$scope.canGenerate = false;

$scope.$watch('gridView1.checkbox.chk', function(n) {
    if ($scope.mode == 'standby') {
        if (!!n && n.length && n.length > 0) {
            $scope.btnTitle = 'Generate ' + n.length + ' Model';
            $scope.canGenerate = true;
        } else {
            $scope.btnTitle = "Please choose model to generate";
            $scope.canGenerate = false;
        }
    } else {
        $scope.canGenerate = false;
        $scope.btnTitle = "Generating " + n.length + ' Model...';
    }
}, true);
$scope.$watch('dataSource1.data', function(n) {
    for (var i in $scope.dataSource1.data) {
        var row = $scope.dataSource1.data[i];
        row.model = row.model.replace(/ /g,'');
    }
}, true);

$scope.genModel = function() {
    if ($scope.gridView1.checkbox.chk.length > 0) {
        $scope.mode = 'gen';
        var current = $scope.gridView1.checkbox.chk.shift();
        current.status = "BUILDING...";
        $http.post(Yii.app.createUrl('/dev/genModel/newAllModel&gen=1'), {
            item: current,
            conn: $scope.dataFilter1.filters[0].value
        }).success(function() {
            current.status = "DONE";
            $scope.genModel();
        });
    } else {
        $scope.mode = 'standby';
        $scope.gridView1.clearCheckbox();
    }
}

$scope.dataSource1.afterQuery = function() {
    for (var i in $scope.dataSource1.data) {
        var row = $scope.dataSource1.data[i];
        row.model = snakeToCamel(row.name);
    }
};