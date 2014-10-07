
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
        return item;
    }

    for (i in $storage.nfy.items) {
        $storage.nfy.items[i] = $scope.processNfy($storage.nfy.items[i]);
    }

    $scope.parseDate = function (date) {
        var t = date.split(/[- :]/);
        var d = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5] || 0);
        return d;
    }
    
//    var sse = new ServerSentEvent(url);
//
//    sse.on('message', function (data) {
//        $scope.$apply(function () {
//            var data = $scope.processNfy(JSON.parse(event.data));
//            $storage.nfy.items.unshift(data);
//            nfyWidget.badge = $storage.nfy.items.length;
//        });
//        console.log(event);
//    });

});