setInterval(function() {
    $.getJSON(Yii.app.createUrl('/sys/profile/GetSystemLoad'), function(res){
        $timeout(function(){
            $scope.os = res.os;
            $scope.cpu = res.cpu;    
            $scope.mem = res.mem;
            $scope.php = res.php;
            $scope.hdd = res.hdd;
        });
    });
}, 800);