
function check() {
    var url = Yii.app.createUrl('/install/default/finish', {s: 1});
    $http.get(url).success(function (data) {
        if (data != "install") {
            location.href = Yii.app.createUrl('/site/login');
        } else {
            $timeout(function () {
                check();
            }, 1000);
        }
    });
}

check();