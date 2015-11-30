$scope.parent = window.opener.parentScope;
if (!$scope.parent.active) {
    window.close();
}

$scope.active = $scope.parent.active;
$scope.rowHeaders = $scope.active.gridOptions.rowHeaders || 1;
$scope.rowHeadersArray = [];
$scope.selected = null;
$scope.activeRow = null;
$scope.activeCol = null;

window.$(document).keydown(function (event) {
    if (!(String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19)) return true;
    
    $scope.parent.save();
    event.preventDefault();
    return false;
});

$scope.scanColSpan = function(row) {
    var colSpanCount = 0;
    for (var c in $scope.active.columns) {
        var col = $scope.active.columns[c];
        if (!col.headers) {
            col.headers = {};
        }
        if (!col.headers['r' + row] 
            || !col.headers['r' + row].colSpan 
            || typeof col.headers['r' + row].label == "undefined") {
                
            col.headers['r' + row] = {
                colSpan: 0,
                label: ''
            };
        }
        
        var head = col.headers['r' + row];
        
        if (colSpanCount < 1) {
            if (head.colSpan * 1 > 1) {
                colSpanCount = (head.colSpan * 1) -1;
            } else {
                head.colSpan = 1;
            }
        } else {
            head.colSpan = -1;
            colSpanCount -= 1;
        }
        
    }
}
$scope.setRowHeaders = function(num) {
    $scope.rowHeaders = num;
    $scope.active.gridOptions.rowHeaders = num;
    
    $scope.rowHeadersArray = [];
    for (var i = 0; i < $scope.rowHeaders; i++) {
        $scope.rowHeadersArray.unshift(i + 1);
    }
    $scope.scanColSpan(num);
    $scope.parent.save();
}
$scope.setRowHeaders($scope.rowHeaders);

$scope.getColSpan = function(row, col) {
    if (row == 1) {
        return 1;
    }

    if (!col.headers) {
        col.headers = {};
    }
    if (!col.headers['r' + row]) {
        col.headers['r' + row] = {
            colSpan: 0,
            label: ''
        };
    }
    return col.headers['r' + row].colSpan;
}
$scope.getWidth = function(c) {
    if (c.options && c.options.width) {
        return c.options.width;
    }
    return '~';
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
    $scope.selected = $scope.active.columns[col];
    
    if (!$scope.selected.options) {
        $scope.selected.options = {};
    }
    
    if (!$scope.selected.options.width) {
        $scope.selected.options.width = ''
    }

    if (row > 1) {
        if (!$scope.selected.headers) {
            $scope.selected.headers = {};
        }
        
        if (!$scope.selected.headers['r' + row]) {
            $scope.selected.headers['r' + row] = {
                colSpan: 0,
                label: ''
            };
        }
        
        if (!$scope.selected.headers['r' + row].label) {
            $scope.selected.headers['r' + row].label = '';
        }
        
        $scope.selected = $scope.selected.headers['r' + row];
        $timeout(function() {
            if (!!$scope.selected.headers) {
                $scope.selected = $scope.selected.headers['r' + row];
            }
        });
    }
    return false;
}