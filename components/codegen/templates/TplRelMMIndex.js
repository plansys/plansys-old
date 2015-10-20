$scope.choose = function() {
    $scope.parentWindow.$apply(function() {
        var a = $scope.parentWindow.dsParent.data;
        var b = $scope.gridView1.checkbox.chk;
        a.push.apply(a,b);
        window.close();
    });
}

$scope.excludeID = function () {
    var ids = [];
    var parentPK = $scope.parentWindow.dsParent.primaryKey;
    $scope.parentWindow.dsParent.data.forEach(function (item) {
        ids.push(item[parentPK]);
    });
    return ids;
}