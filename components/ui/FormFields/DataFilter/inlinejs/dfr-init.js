/**** clean data-filter column options by deleting unnecesary options ****/
var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());

for (i in $scope.$parent.value) {
    var item = $scope.$parent.value[i];
    for (k in item) {
        if (['filterType', 'name', 'label', 'show', 'isCustom', 'resetable', 'options'].indexOf(k) < 0
                && templateAttr.typeOptions[item.filterType].indexOf(k) < 0) {
            delete item[k];
        }
    }

    for (k in templateAttr.typeOptions[item.filterType]) {
        var prop = templateAttr.typeOptions[item.filterType][k];
        if (!item[prop]) {
            item[prop] = templateAttr[prop];
        }
    }
    
    $scope.$parent.save();
}

delete $scope.$parent.$parent.filterRelation;