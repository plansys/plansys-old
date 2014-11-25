$scope.body = function (value, row) {
    value = JSON.parse(value);
    url = Yii.app.createUrl('/widget/NfyWidget.read', {nid: row.entity.id});

    return '<a href="' + url + '" ng-click="$event.preventDefault()">' + value.message + '</a>';
};