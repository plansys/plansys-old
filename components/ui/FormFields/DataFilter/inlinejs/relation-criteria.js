$scope.$watch('item.relModelClass', function (newv, oldv) {
    if (newv != oldv) {
        $scope.modelClass = newv;
        $scope.value = $scope.item.relCriteria;
    }
}, true);
$scope.modelClass = $scope.item.relModelClass;
$scope.value = $scope.item.relCriteria;
$scope.$watch('item.relParams', function (newv, oldv) {
    if (newv != oldv) {
        $scope.getPreviewSQL();
    }
}, true);
$scope.$watch('item.relIncludeEmpty', function (newv, oldv) {
    function isWrong(val) {
        if (val == '' || !val) {
            return true;
        }
        return false;
    }
    if (newv == 'Yes' && isWrong($scope.item.relEmptyValue) && isWrong($scope.item.relEmptyLabel)) {
        $scope.item.relEmptyValue = 'null';
        $scope.item.relEmptyLabel = '-- NONE --';
    }
}, true);