$scope.menu = true;
$scope.mobileselect = false;
$scope.tree.expandAll(function(item) {
    item.canExpand = false;
}, function() {
    if ($scope.params.id) {
        $scope.tree.selectBy('id', $scope.params.id);
    }
});

$scope.changed = function(item) {
    $scope.mobileselect = true;
    console.log("SELECTED");
}

$scope.toggleMenu = function() {
    $scope.menu = !$scope.menu;
    $scope.mobileselect = false;
}