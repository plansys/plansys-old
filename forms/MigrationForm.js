$scope.loading = false;
$scope.status = "Last Migration #" + $scope.model.idx;
$scope.migration = $scope.model.isNew;

$scope.toggleMigration = function () {
    $scope.migration = !$scope.migration;
}

$scope.runInternal = function (id, file) {
    $scope.loading = true;
    if (file != null) {
        $scope.status = "Executing: #" + id + " - " + file + " ...";
    } else {
        $scope.status = "Executing: #" + id + " ...";
    }

    $http.get(Yii.app.createUrl('/migration', {
        id: id,
        file: file
    })).success(function () {
        $scope.loading = false;
        if (file != null) {
            $scope.status = "Migration: #" + id + " - " + file + " Successfully executed!";
        } else {
            $scope.status = "Migration: #" + id + " Successfully executed!";
        }
    });
}

$scope.run = function (e, id, file) {
    e.stopPropagation();
    e.preventDefault();

    $scope.runInternal(id, file);
}