
app.directive('uploadFile', function($timeout, $upload, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                $scope.filePath = null;
                $scope.path = $el.find("data[name=path]").html().trim();
                $scope.repoPath = $el.find("data[name=repo_path]").html().trim();
                $scope.update = $el.find("data[name=file_update]").html().trim();
                $scope.fileType = $el.find("data[name=file_type]").html().trim();
                $scope.file = null;
                $scope.loading = false;
                $scope.progress = -1;
                $scope.errors = [];
                $scope.json;
                $scope.saveDesc = function(desc){
                    var request = $http({
                        'method' : 'post',
                        'url' : Yii.app.createUrl('/formField/uploadFile.description'),
                        'data' : {'desc':$scope.encode(desc),
                                  'name':$scope.encode($scope.file.name),
                                  'path' :$scope.encode($scope.path)
                                 }
                    });
                };
                $scope.onFileSelect = function($files) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        var type = null;
                        var ext = $scope.ext(file);
                        if ($scope.fileType === "" || $scope.fileType === null) {
                            $scope.upload(file);
                        } else {
                            type = $scope.fileType.split(',');
                            for (var i = 0; i < type.length; i++)
                                type[i] = type[i].trim();

                            if ($.inArray(ext, type) > -1) {
                                $scope.upload(file);
                            } else {
                                $scope.errors.push("Tipe file tidak diijinkan, File yang diijinkan adalah " + $scope.fileType);
                            }
                        }
                    }
                };

                $scope.encode = function(input) {
                    var output = "";
                    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                    var i = 0;
                    var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
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
                                _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +
                                _keyStr.charAt(enc3) + _keyStr.charAt(enc4);

                    }

                    return output;
                };

                $scope.remove = function(file) {
                    $scope.loading = true;
                    $scope.errors = [];
                    var request = $http({
                        method: "post",
                        url: Yii.app.createUrl('/formField/uploadFile.remove'),
                        data: {file: $scope.encode(file)}
                    });
                    request.success(
                            function(html) {
                                $scope.file = null;
                                $scope.loading = false;
                            }
                    );

                };
                
                $scope.upload = function(file) {
                    $scope.errors = [];
                    $scope.loading = true;
                    $scope.progress = 0;
                    $upload.upload({
                        url: Yii.app.createUrl('/formField/uploadFile.upload', {'path': $scope.encode($scope.path)}),
                        data: {myObj: $scope.myModelObj},
                        file: file
                     }).progress(function(evt) {
                        $scope.progress =  parseInt(100.0 * evt.loaded / evt.total);
                     }).success(function(data,html) {
                        $scope.progress = 101;
                        $scope.file = {
                            'name': data,
                            'path': $scope.path + '/' + data
                        };
                        $scope.filePath = $scope.path.replace($scope.repoPath, '').replace(/\\/g, "/") + '/' + $scope.file.name;
             
                        $scope.icon($scope.file);
                        $scope.loading = false;
                        $scope.progress = -1;
                        
                     });
                    
                };
                $scope.ext = function(file) {
                    var type = file.name.split('.');
                    if (type.length === 1 || (type[0] === "" && type.length === 2)) {
                        return "";
                    }
                    return type.pop();
                }

                $scope.icon = function(file) {
                    var type = $scope.ext(file);

                    var code = ['php', 'js', 'html', 'json'];
                    var archive = ['zip'];
                    var image = ['jpg', 'jpeg', 'png', 'bmp'];
                    var audio = ['mp3'];
                    var video = ['avi'];
                    var word = ['doc', 'docx'];
                    var text = ['txt'];
                    var excel = ['xls', 'xlsx'];
                    var ppt = ['ppt', 'pptx'];
                    var pdf = ['pdf'];

                    if ($.inArray(type, image) > -1) {
                        $scope.file.type = "image";
                    } else if ($.inArray(type, code) > -1) {
                        $scope.file.type = "code";
                    } else if ($.inArray(type, archive) > -1) {
                        $scope.file.type = "archive";
                    } else if ($.inArray(type, audio) > -1) {
                        $scope.file.type = "audio";
                    } else if ($.inArray(type, video) > -1) {
                        $scope.file.type = "movie";
                    } else if ($.inArray(type, word) > -1) {
                        $scope.file.type = "word";
                    } else if ($.inArray(type, text) > -1) {
                        $scope.file.type = "text";
                    } else if ($.inArray(type, excel) > -1) {
                        $scope.file.type = "excel";
                    } else if ($.inArray(type, ppt) > -1) {
                        $scope.file.type = "powerpoint";
                    } else if ($.inArray(type, pdf) > -1) {
                        $scope.file.type = "pdf";
                    } else {
                        $scope.file.type = "file";
                    }
                };
                if ($scope.update !== "") {
                    var request = $http({
                        method: "post",
                        url: Yii.app.createUrl('/formField/uploadFile.checkFile'),
                        data: {file: $scope.encode($scope.update)}
                    });
                    request.success(
                            function(html) {
                                if (html === 'exist') {
                                    var name = $scope.update.split('/');
                                    name = name[name.length - 1];
                                    var path = $scope.repoPath + $scope.update;
                                    $scope.file = {
                                        'name': name,
                                        'path': path
                                    };
                                    $scope.icon($scope.file);
                                } else {
                                    $scope.file = null;
                                }
                            }
                    );
                } else {
                    $scope.file = null;
                }
            };
        }
    };
});