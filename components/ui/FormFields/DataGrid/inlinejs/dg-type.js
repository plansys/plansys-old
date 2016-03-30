
/**** clean data-grid column options by deleting unnecesary options ****/
var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());

for (i in $scope.$parent.item) {
    if (['columnType', 'name', 'label', 'show', 'mergeSameRow','mergeSameRowWith','html'].indexOf(i) < 0 && templateAttr.typeOptions[$scope.item.columnType].indexOf(i) < 0) {
        delete $scope.$parent.value[$scope.$index][i];
    }
}
for (i in templateAttr.typeOptions[$scope.item.columnType]) {
    var prop = templateAttr.typeOptions[$scope.item.columnType][i];
    if (!$scope.$parent.value[$scope.$index][prop]) {
        $scope.$parent.value[$scope.$index][prop] = templateAttr[prop];
    }
}
