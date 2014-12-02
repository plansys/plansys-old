app.directive('repoDialog', function ($timeout, $compile, $http) {
    return {
        require: '?ngModel',
        scope: true,
        templateUrl: Yii.app.createUrl('/formfield/RepoBrowser.browse'),
        compile: function (element, attrs, transclude) {
            $container = element.find(".modal-container");
            return function ($scope, $el, attrs, ctrl) {

                $scope.close = function (f) {
                    $el.find('.modal-container').css('display', 'none');
                    if (f) {
                        f($scope);
                    } else {
                        $timeout(function () {
                            if ($scope.afterChoose) {
                                $scope.afterChoose({});
                            }
                        });
                    }
                };

                $scope.choose = function (file) {
                    $scope.close(function () {
                        $timeout(function () {
                            if ($scope.afterChoose) {
                                $scope.afterChoose(file);
                            }
                        });
                    });

                }

                $scope.open = function (f) {
                    $el.find('.modal-container').css('display', 'block');
                    if (f) {
                        f($scope);
                    }
                };

                $scope.icon = {
                    'loading': '<i class=\'fa fa-nm fa-folder-open\'></i>',
                    'dir': '<i class=\'fa fa-nm fa-folder\'></i>',
                    '*': '<i class=\'fa fa-nm fa-file-o\'></i>',
                    'rx:/php|css|js|html/i': '<i class=\'fa fa-nm fa-file-code-o\'></i>',
                    'rx:/png|jpg|tif|jpeg|psd|gif|exif|bmp|tga/i': '<i class=\'fa fa-nm fa-file-image-o\'></i>',
                };
                $scope.stringAlias = function (value, arr) {
                    var wildCard = false;
                    for (k in arr) {
                        if (k.toLowerCase() == value.toLowerCase()) {
                            return arr[k];
                        }
                        if (k.indexOf('rx:') == 0) {
                            eval("var regex = " + k.substr(3));
                            var match = value.match(regex);
                            if (match != null && match.length > 0) {
                                return arr[k];
                            }
                        }
                        if (k == '*') {
                            wildCard = arr[k];
                        }
                    }

                    if (wildCard)
                        return wildCard;
                    return value;
                }
                $scope.loading = false;
                $scope.path = "/";
                var t = null;
                $timeout(function () {

                    $scope.data = JSON.parse($el.find("data[name=repodata]").text());
                    $scope.gridOptions = {
                        data: 'data',
                        multiSelect: false,
                        afterSelectionChange: function (r) {
                            clearTimeout(t);
                            t = setTimeout(function () {
                                if ($scope.selected != r.entity.name) {
                                    $scope.selected = r.entity.name;
                                    return false;
                                }
                                if (r.entity.type == "dir") {
                                    $http.get(Yii.app.createUrl('/repo/changeDir', {dir: r.entity.path || ''})).success(function (data) {
                                        $scope.path = data.path;
                                        $scope.data = data.item;
                                        if (!$scope.data) {
                                            $scope.data = [];
                                        }
                                        if (data.parent != "") {
                                            $scope.data.unshift({
                                                name: "..",
                                                path: data.parent,
                                                size: 0,
                                                type: "dir"
                                            });
                                        }
                                        $scope.selected = null;
                                    });
                                } else {
                                    $scope.choose(r.entity);
                                    $scope.selected = null;
                                }
                            }, 10);
                        },
                        columnDefs: [
                            {
                                field: 'type',
                                displayName: '',
                                width: 25,
                                cellTemplate: "<div class=\"ngCellText\" ng-class=\"col.colIndex()\"><span ng-cell-text ng-bind-html=\"stringAlias(COL_FIELD, icon)\"></span></div>"
                            },
                            {
                                field: 'name',
                                displayName: 'File Name'
                            },
                            {
                                field: 'size',
                                displayName: 'Size',
                                cellFilter: 'fileSize',
                                width: 100,
                            },
                        ]
                    };
                    $scope.gridReady = true;
                });
            }
        }
    };
});