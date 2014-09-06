

$scope.changePhone = function(id) {
    console.log(id);

    $scope.dataSource1.updateParam('id', id, 'where');
    $scope.dataSource1.updateParam('order_by', 'id', 'order');
    $scope.dataSource1.query();
}