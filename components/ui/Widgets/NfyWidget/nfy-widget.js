
app.controller("NfyWidgetController", function ($scope, $http, $timeout, $localStorage) {

    $storage = $localStorage;
    $scope.$storage = $storage;

    if (!$storage.nfy) {
        $storage.nfy = {};
    }

    $scope.parseDate = function (date) {
        var t = date.split(/[- :]/);

        var d = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5] || 0);

        return d;
    }


    var sse = new EventSource(Yii.app.createUrl('/widget/NfyWidget.stream'));
    sse.addEventListener('message', function (e) {
        $scope.$apply(function () {

            items = JSON.parse(e.data);
            for (var i in items) {
                if (!$storage.nfy || !$storage.nfy.items) {
                    $storage.nfy = {items: []};
                }

                $storage.nfy.items.push(items[i]);
            }
            console.log(items);
        });
    }, false);


});