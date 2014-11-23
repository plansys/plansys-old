$scope.body = function(value, row) {
    value = JSON.parse(value);
    return '<a href="'+value.url+'" ng-click="$event.preventDefault()">' + value.message + '</a>';
};