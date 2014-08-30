
app.directive('uploadFile', function($timeout, $upload) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                $scope.file = null;
                $scope.path = $el.find("data[name=path]").html().trim();
                $scope.repoPath = $el.find("data[name=repo_path]").html().trim();
                $scope.onFileSelect = function($files) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        console.log(file.name);
                        $scope.upload(file);
                    }
                    console.log($scope.path);
                };

                $scope.upload = function(file) {
                    $upload.upload({
                        url: Yii.app.createUrl('/formField/uploadFile.upload', {'path': $scope.path}),
                        data: {myObj: $scope.myModelObj},
                        file: file
                    }).progress(function(evt) {
                        console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                    }).success(function(data, status, headers, config) {
                        $scope.file = $scope.path.replace($scope.repoPath, '').replace(/\\/g,"/") + '/' + file.name;
                        console.log($scope.file);
                    });
                };
            };
        }
    };
});