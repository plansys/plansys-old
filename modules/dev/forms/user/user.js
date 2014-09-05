console.log($scope.dataGrid1);
$scope.dataGrid1.onGridLoaded = function(opt) {

    for (i in opt.columnDefs) {
        opt.columnDefs[i].visible = false;
    }
    opt.columnDefs[1].visible = true;

}

