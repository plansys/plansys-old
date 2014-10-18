
app.controller("NfyWidgetController", function ($scope, $http, $timeout, $localStorage) {

    var http = location.protocol;
    var slashes = http.concat("//");
    var host = slashes.concat(window.location.hostname);
    var url = host + ":8981/" + $("#nfy-uid").text().trim();

    nfyWidget = $storage.widget.list.NfyWidget.widget;
    $storage = $localStorage;
    $scope.$storage = $storage;
    $scope.error = false;

    $storage.nfy = $storage.nfy || {};
    $storage.nfy.items = JSON.parse($("#nfy-data").text().trim());
    nfyWidget.badge = $storage.nfy.items.length;


    $scope.processNfy = function (item) {
        item.url = Yii.app.createUrl('/widget/NfyWidget.read', {
            nid: item.id
        });
        if (typeof item.body == "string") {
            item.body = JSON.parse(item.body);
        }
        return item;
    }

    for (i in $storage.nfy.items) {
        $storage.nfy.items[i] = $scope.processNfy($storage.nfy.items[i]);
    }

    $scope.parseDate = function (date) {
        console.log(date);
        if (typeof date == "string") {
            var t = date.split(/[- :]/);
            var d = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5] || 0);
            return d;
        }
    }

    var eSource = new EventSource(url);
    //detect message receipt
    eSource.onmessage = function (event) {
        var json = JSON.parse(event.data);
        var data = $scope.processNfy(json[0]);
        $scope.$apply(function () {
            $storage.nfy.items.unshift(data);
            nfyWidget.badge = $storage.nfy.items.length;
        });
    };

    var startTrigger = false;
    eSource.onerror = function (e) {
        if (!startTrigger) {
            $http.get(Yii.app.createUrl('/nfy/start')).success(function (data) {
                startTrigger = false;
            });
            startTrigger = true;
        }
    };


});