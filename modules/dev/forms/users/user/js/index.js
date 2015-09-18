
$scope.dataGrid1.onGridLoaded = function (options) {

}

$scope.dataGrid1.afterCellEdit = function (val, row, col, data) {
    console.log(val, row, col, data);
}
