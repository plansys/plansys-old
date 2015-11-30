app.directive('uploadFile', function ($timeout, $upload, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.file = null;
                $scope.loading = true;
                $scope.progress = -1;
                $scope.errors = [];
                $scope.json;

                //default value
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.classAlias = $el.find("data[name=class_alias]").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.mode = $el.find("data[name=mode]").html().trim();
                $scope.allowDelete = $el.find("data[name=allow_delete]").html().trim();
                $scope.allowOverwrite = $el.find("data[name=allow_overwrite]").html().trim();
                $scope.fileType = $el.find("data[name=file_type]").html().trim();
                $scope.options = JSON.parse($el.find("data[name=options]").text());

                $scope.choosing = "";
                $scope.choose = function (c) {
                    $scope.errors.length = 0;
                    $scope.choosing = c;
                    switch (c) {
                        case "Browse":
                            $timeout(function () {
                                $scope.BrowseDialog.afterChoose = function (f) {
                                    $timeout(function () {
                                        if (typeof f.name != "undefined") {
                                            $scope.value = f.path;
                                            $scope.file = {
                                                'name': f.name,
                                                'downloadPath': f.downloadPath
                                            };
                                            $scope.icon(f.name);
                                        } else {
                                            $scope.choose('');
                                        }
                                    });
                                }
                                $scope.BrowseDialog.open();
                            });
                            break;
                        case "":
                            if ($scope.file && $scope.value) {
                                $scope.oldFile = $scope.file;
                            }

                            $scope.file = null;
                            break;
                        case "undo":
                            $scope.file = $scope.oldFile;

                    }
                }

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if (typeof ctrl.$viewValue != "undefined") {
                            if (ctrl.$viewValue != null && ctrl.$viewValue != '') {
                                $scope.value = ctrl.$viewValue;
                                $scope.file = {
                                    name: $scope.value
                                };
                                $scope.checkFile();
                            }
                        }
                    };
                }

                //Saving file description to JSON
                $scope.saveDesc = function (desc) {
                    $scope.fileDescLoadText = '...';
                    $http({
                        'method': 'post',
                        'url': Yii.app.createUrl('/formfield/UploadFile.description'),
                        'data': {
                            'desc': desc,
                            'name': $scope.file.name
                        }
                    }).success(function () {
                        $scope.fileDescLoadText = '';
                    });
                };

                //Upload Funcs start
                $scope.onFileSelect = function ($files) {
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

                $scope.formatName = function (name) {
                    if (!name) {
                        return '';
                    }
                    return name.split("/").pop().split("\\").pop();
                }

                $scope.upload = function (file) {
                    $scope.errors = [];
                    $scope.loading = true;
                    $scope.progress = 0;
                    $scope.$parent.uploading.push($scope.name);
                    $scope.thumb = '';
                    $upload.upload({
                        url: Yii.app.createUrl('/formfield/UploadFile.upload', {
                            class: $scope.classAlias,
                            name: $scope.name
                        }),
                        file: file
                    }).progress(function (evt) {
                        $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
                    }).success(function (data, html) {
                        $scope.progress = 101;
                        var ext = $scope.ext(data);

                        if (data.success == 'Yes') {
                            $scope.value = data.path;
                            $scope.file = {
                                'name': data.name,
                                'downloadPath': data.downloadPath
                            };

                            $scope.icon($scope.file);

                            if (['jpg', 'gif', 'png', 'jpeg'].indexOf(ext) >= 0) {
                                $scope.getThumb();
                            }
                            ctrl.$setViewValue(data.path);
                        } else {
                            alert("Error Uploading File. \n");
                        }

                        $scope.loading = false;
                        $scope.progress = -1;
                        

                        var index = $scope.$parent.uploading.indexOf($scope.name);
                        if (index > -1) {
                            $scope.$parent.uploading.splice(index, 1);
                        }
                    }).error(function (data) {
                        $scope.progress = -1;
                        $scope.loading = false;
                        var index = $scope.$parent.uploading.indexOf($scope.name);

                        if (index > -1) {
                            $scope.$parent.uploading.splice(index, 1);
                        }
                        alert("Upload Failed");

                    });
                };

                $scope.remove = function (file) {
                    if ($scope.choosing == 'Browse') {
                        $scope.choose('');
                    } else if (confirm("Are you sure want to remove this file ?")) {
                        $scope.loading = true;
                        $scope.errors = [];
                        var request = $http({
                            method: "post",
                            url: Yii.app.createUrl('/formfield/UploadFile.remove'),
                            data: {file: file}
                        }).success(function (html) {
                            $scope.choose('');
                            $scope.file = null;
                            $scope.value = '';
                            ctrl.$setViewValue('');
                            $scope.loading = false;
                        }).error(function () {
                            $scope.loading = false;
                        });
                    }
                };

                $scope.thumb = '';
                $scope.getThumb = function () {
                    if ($scope.file.downloadPath) {
                        $http.get(Yii.app.createUrl('/formfield/UploadFile.thumb', {
                            't': $scope.file.downloadPath
                        })).success(function (url) {
                            $scope.thumb = url;
                        });
                    }
                }

                //Get the file extension
                $scope.ext = function (file) {
                    var type = '';
                    if (typeof file.name == 'string') {
                        type = file.name.split('.');
                    }
                    if (typeof file == 'string') {
                        type = file.split('.');
                    }

                    if (type.length === 1 || (type[0] === "" && type.length === 2)) {
                        return "";
                    }

                    return type.pop().toLowerCase();
                };

                //Create icon based on extension 
                $scope.icon = function (file) {
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
                        $scope.file.type = "fa-file-image-o";
                    } else if ($.inArray(type, code) > -1) {
                        $scope.file.type = "fa-file-code-o";
                    } else if ($.inArray(type, archive) > -1) {
                        $scope.file.type = "fa-file-archive-o";
                    } else if ($.inArray(type, audio) > -1) {
                        $scope.file.type = "fa-file-audio-o";
                    } else if ($.inArray(type, video) > -1) {
                        $scope.file.type = "fa-file-movie-o";
                    } else if ($.inArray(type, word) > -1) {
                        $scope.file.type = "fa-file-word-o";
                    } else if ($.inArray(type, text) > -1) {
                        $scope.file.type = "fa-file-text-o";
                    } else if ($.inArray(type, excel) > -1) {
                        $scope.file.type = "fa-file-excel-o";
                    } else if ($.inArray(type, ppt) > -1) {
                        $scope.file.type = "fa-file-powerpoint-o";
                    } else if ($.inArray(type, pdf) > -1) {
                        $scope.file.type = "fa-file-pdf-o";
                    } else {
                        $scope.file.type = "fa-file-o";
                    }
                };

                //check if file is defined from outside
                $scope.checkFile = function () {
                    if ($scope.value != "") {
                        $scope.thumb = '';
                        var request = $http({
                            method: "post",
                            url: Yii.app.createUrl('/formfield/UploadFile.checkFile'),
                            data: {
                                file: $scope.value
                            }
                        });
                        request.success(function (result) {
                            if (result.status === 'exist') {
                                $scope.file.downloadPath = result.downloadPath;
                                $scope.icon($scope.file);
                                var ext = $scope.ext($scope.file);
                                if (['jpg', 'gif', 'png', 'jpeg'].indexOf(ext) >= 0) {
                                    $scope.getThumb();
                                }
                            } else {
                                $scope.file = null;
                                $scope.value = '';
                                ctrl.$setViewValue('');
                            }
                            $scope.loading = false;
                        });
                    } else {
                        $scope.file = null;
                        $scope.loading = false;
                    }
                };

                $scope.checkFile();

                if ($scope.options['ps-mode']) {
                    $scope.mode = $scope.$parent[$scope.options['ps-mode']];
                    $scope.$watch('$parent.' + $scope.options['ps-mode'], function (n, o) {
                        if (n !== o) {
                            $timeout(function () {
                                $scope.mode = n;
                            });
                        }
                    }, true);
                }

                var parent = $scope.getParent($scope);
                parent[$scope.name] = $scope;
            };
        }
    };
});