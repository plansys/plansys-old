

$scope.changePhone = function(id) {
    console.log(user.id);

    $scope.dataSource1.updateParam('id', id, 'where');
    $scope.dataSource1.updateParam('order_by', 'id', 'order');
    $scope.dataSource1.query();
}
$scope.$on('ngGridEventEndCellEdit', function(event) {
//event.targetScope.row.entity[];
    console.log(event.targetScope.col);
    // console.log($scope.contact );
});