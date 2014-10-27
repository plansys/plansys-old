var active = $scope.active;

function updateRelationCriteria() {
    url = Yii.app.createUrl('/formfield/DataSource.relClass', {
        class: $scope.classPath,
        rel: active.relationTo
    })
    $.get(url, function (data) {
        $scope.modelClass = data;
    });
}

$scope.$watch('active.relationTo', function (newv, oldv) {
    if (newv != oldv) {
        updateRelationCriteria();
    }
}, true);

updateRelationCriteria();