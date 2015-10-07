$(window).resize(function () {
    $("#properties").width($("#properties").parent().width());
    $(".ngTopPanel").css('top', top);
}).resize();

var repoClickTimeout = null;
$scope.selected = null;
$scope.lastSelectedPath = null;
$scope.setCurrentDir = function (dir) {
    $scope.currentDir = dir.replace(/\\/g, '/');
    $scope.dirs = [];
    var dirs = $scope.currentDir.split("/");
    var d = "";
    dirs.forEach(function (item, k) {
        if (item == "") {
            return;
        }
        if (k > 1) {
            d += "/" + item;
        }

        $scope.dirs.push({
            name: item,
            path: d,
        });
    })
}
$scope.setCurrentDir($scope.params.currentDir);

$scope.thumb = '';

$scope.click = function (row) {


    $scope.selected = row.entity;
    clearTimeout(repoClickTimeout);
    repoClickTimeout = setTimeout(function () {

        // get thumbnail
        $scope.thumb = $scope.dataGrid1.stringAlias($scope.selected.type, 'type').replace('fa-nm', 'fa-99x');
        $http.get(Yii.app.createUrl('/formfield/UploadFile.thumb', {
            't': $scope.encode($scope.selected.path)
        })).success(function (url) {
            if (url != "") {
                $scope.thumb = '<img src="' + url + '" />';
            }
        });

        // change dir
        if ($scope.selected.type == "dir") {
            if ($scope.lastSelectedPath == $scope.selected.path) {
                $scope.selected.type = "loading";
                $http.get(Yii.app.createUrl('/repo/changeDir', {dir: $scope.selected.path})).success(function (data) {
                    $scope.setCurrentDir($scope.selected.path);
                    $scope.dataSource1.data = data.item;
                    if (!$scope.dataSource1.data) {
                        $scope.dataSource1.data = [];
                    }
                    if (data.parent != "") {
                        $scope.dataSource1.data.unshift({
                            name: "..",
                            path: data.parent,
                            size: 0,
                            type: "dir"
                        });
                    }
                    $scope.selected.type = "dir";
                    $("#col1").scrollTop(0);
                });
                $scope.lastSelectedPath = null;
            } else {
                $scope.lastSelectedPath = $scope.selected.path;
            }
        }
    }, 100);
}

$scope.getDownloadUrl = function (item) {
    var url = Yii.app.createUrl('/repo/download', {
        n: item.name,
        f: $scope.encode(item.path)
    });

    return url;
}

//Decode Encode to Base64 start
$scope._keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
$scope.encode = function (input) {
    var output = "";
    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
    var i = 0;
    while (i < input.length) {

        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
            enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
            enc4 = 64;
        }

        output = output +
                $scope._keyStr.charAt(enc1) + $scope._keyStr.charAt(enc2) +
                $scope._keyStr.charAt(enc3) + $scope._keyStr.charAt(enc4);

    }

    return output;
};

$scope.decode = function (input) {
    var output = "";
    var chr1, chr2, chr3;
    var enc1, enc2, enc3, enc4;
    var i = 0;

    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    while (i < input.length) {

        enc1 = $scope._keyStr.indexOf(input.charAt(i++));
        enc2 = $scope._keyStr.indexOf(input.charAt(i++));
        enc3 = $scope._keyStr.indexOf(input.charAt(i++));
        enc4 = $scope._keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
            output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output = output + String.fromCharCode(chr3);
        }

    }

    return output;

};