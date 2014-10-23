/**** clean data-grid column options by deleting unnecesary options ****/
var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
var relFields = ['relParams','relModelClass', 'relIdField', 'relLabelField', 'relCriteria'];

if ($scope.item.filterType != "relation") {
    for (i in relFields) {
        var v = relFields[i];
        if ($scope.item[v]) {
            delete $scope.$parent.item[v];
        }
    }
} else {
    for (i in relFields) {
        var v = relFields[i];
        if (!$scope.item[v]) {
            $scope.$parent.item[v] = templateAttr[v];
        }
    }
}
console.log($scope.$parent.item);