$scope.parent = window.opener.parentScope;
if (!$scope.parent.active) {
    window.close();
}

$scope.active = $scope.parent.active;
$scope.rowHeaders = $scope.active.gridOptions.rowHeaders || 1;
$scope.selected = null;
$scope.activeRow = null;
$scope.activeCol = null;

$scope.setRowHeaders = function(num) {
    if (num == 1) {
        delete $scope.active.gridOptions.rowHeaders;
        $scope.rowHeaders = 1;
    } else {
        $scope.rowHeaders = num;
        $scope.active.gridOptions.rowHeaders = num;
    }
    $scope.rowHeadersArray = [];
    for (var i = 0; i < $scope.rowHeaders; i++) {
        $scope.rowHeadersArray.unshift(i + 1);
    }
    $scope.parent.save();
}
$scope.setRowHeaders($scope.rowHeaders);

$scope.getWidth = function(c) {
    if (c.options.width) {
        return c.options.width;
    }
    return '~';
}
$scope.getTooltip = function(row, col) {
    return "HELLO";
}
$scope.isActive = function(row, col) {
    if (col == $scope.activeCol && row == $scope.activeRow) {
        return true;
    }
    return false;
}
$scope.select = function(row,col,e) {
    e.preventDefault();
    e.stopPropagation();
    
    $scope.activeRow = row;
    $scope.activeCol = col;
    return false;
}