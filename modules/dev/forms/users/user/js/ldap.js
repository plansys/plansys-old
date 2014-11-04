$scope.q = "";
$scope.search = function (search) {
    var s = search || '*';

    if (s.indexOf("*") < 0) {
        s = "*" + s + "*";
    }

    $scope.formSubmitting = true;

    $http.get(Yii.app.createUrl('/dev/user/ldapSearch', {'q': s})).success(function (data) {
        $timeout(function () {
            $scope.dataSource1.data = data;
            $scope.formSubmitting = false;
        });
    });
};
$scope.dataSource1.data = $scope.params.data;

$("form").submit(function (e) {
    e.preventDefault();
    return false;
});