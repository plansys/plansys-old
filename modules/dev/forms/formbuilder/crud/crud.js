if (!window.opener || !window.opener.activeScope) {
    window.close();
}
var activeScope = window.opener.activeScope;
function generateClassPrefix(s) {
    var parts = s.split(".");

    if (parts[1] == "forms") {
        var module = parts.shift();
        parts = parts.splice(1);
        parts.unshift(module);
        parts = parts.join(" ").trim().replace(/\s+/g, '.');
    } else if (parts[1] == "modules") {
        parts = parts.splice(2);
        var module = parts.shift();
        parts = parts.splice(2);
        parts.unshift(module);
        parts = parts.join(" ").trim().replace(/\s+/g, '.');
    }

    var result = parts.replace(/(\.\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    return result.charAt(0).toUpperCase() + result.slice(1);
}

$scope.getModulePath = function () {
    var path = activeScope.activeItem.alias;
    var module = path.split(".");
    if (path.indexOf('modules') > 0 && module.length >= 3) {
        return module.splice(0, 3).join(".");
    } else {
        return module[0];
    }
}
$scope.getControllerUrl = function () {
    var module = '';
    var mp = $scope.getModulePath().split(".");
    if ($scope.getModulePath().length > 3) {
        module = mp.pop() + "/";
    }

    if (!!$scope.model) {
        return Yii.app.createUrl(module + $scope.model.lcName + "/index");
    }
}
$scope.generateControllerPath = function () {
    return $scope.getModulePath() + ".controllers";
}
$scope.params.prefix = generateClassPrefix(activeScope.activeItem.alias);
$scope.params.alias = activeScope.activeItem.alias;
$scope.data = {};
$scope.exists = [];
$scope.step = '1';
$scope.msg = '';
$scope.fieldList = [];

$scope.resetData = function () {
    $scope.data = {
        path: trim($scope.params.alias, '.') + '.' + $scope.model.lcName,
        files: []
    };

    $scope.dirName = [];
    var markDirName = false
    $scope.data.path.split(".").forEach(function (i) {
        if (i == 'forms') {
            markDirName = true;
        } else if (markDirName) {
            $scope.dirName.push(i);
        }
    });
    $scope.dirName = $scope.dirName.join(".");
}
$scope.onNameChange = function () {
    $scope.model.name = ($scope.model.name.charAt(0).toUpperCase() + $scope.model.name.slice(1)).replace(/[^a-z0-9]/gi, '');
    $scope.model.lcName = ($scope.model.name.charAt(0).toLowerCase() + $scope.model.name.slice(1)).replace(/[^a-z0-9]/gi, '');
    $scope.resetData();
}

$scope.checkAll = function (e) {
    var checked = $(e.target).is(':checked');
    $scope.data.files.forEach(function (i) {
        if (i.status == 'exist') {
            i.overwrite = checked;
        }
    });
}
$scope.form.submit = function (f) {
    $scope.resetData();

    // main form data
    $scope.data.files.push({
        name: $scope.data.path,
        type: 'folder'
    });

    if ($scope.model.masterData == 'No') {
        $scope.data.files.push({
            name: $scope.params.prefix + $scope.model.name + 'Index.php',
            className: $scope.params.prefix + $scope.model.name + 'Index',
            extendsName: $scope.model.model,
            type: 'index',
        });
        $scope.data.files.push({
            name: $scope.params.prefix + $scope.model.name + 'Form.php',
            className: $scope.params.prefix + $scope.model.name + 'Form',
            extendsName: $scope.model.model,
            type: 'form',
        });
        $scope.data.files.push({
            name: $scope.model.name + 'Controller.php',
            className: $scope.model.name + 'Controller',
            type: 'controller',
            mode: 'crud',
            formName: $scope.params.prefix + $scope.model.name + 'Form',
            indexName: $scope.params.prefix + $scope.model.name + 'Index',
            alias: $scope.data.path,
            path: $scope.generateControllerPath()
        });
    } else {
        $scope.data.files.push({
            name: $scope.params.prefix + $scope.model.name + 'Master.php',
            className: $scope.params.prefix + $scope.model.name + 'Master',
            extendsName: $scope.model.model,
            type: 'master',
        });
        $scope.data.files.push({
            name: $scope.model.name + 'Controller.php',
            className: $scope.model.name + 'Controller',
            indexName: $scope.params.prefix + $scope.model.name + 'Master',
            type: 'controller',
            mode: 'master',
            alias: $scope.data.path,
            path: $scope.generateControllerPath()
        });

    }

    $scope.step = 2;
    $scope.msg = 'Cheking file availability...';
    $scope.resetCheck();
    $scope.checkNext();
}
$scope.resetCheck = function () {
    $scope.$index = 0;
    $scope.exists = [];
}
$scope.back = function () {
    $timeout(function () {
        $scope.step = 1;
    });
}
$scope.checkNext = function () {
    if ($scope.step != 2) return;

    if ($scope.$index < $scope.data.files.length) {
        $http.post(Yii.app.createUrl("/dev/crud/checkFile"), {
            path: $scope.data.path,
            file: $scope.data.files[$scope.$index]
        }).success(function (res) {
            if (res == "exist" && $scope.data.files[$scope.$index].type != 'folder') {
                $scope.exists.push($scope.data.files[$scope.$index]);
            }
            $scope.data.files[$scope.$index].status = res;
            $scope.$index++;
            $scope.checkNext();
        }).error(function (res) {
            $scope.data.files[$scope.$index].status = 'Failed to check file';
            $scope.$index++;
            $scope.checkNext();
        });
    } else {
        $scope.step = 3;
        $scope.$index = 0;
        if ($scope.exists.length > 0) {
            $scope.msg = 'Some file(s) already exists, choose which file to overwrite';
        } else {
            $scope.msg = 'Ready to Generate';
        }
    }
}

$scope.done = function () {
    window.close();
    window.opener.location.reload();
}
$scope.generateNext = function () {
    if ($scope.step < 4) {
        $scope.step = 4;
    }

    if ($scope.$index < $scope.data.files.length) {
        if (!$scope.data.files[$scope.$index].path) {
            $scope.data.files[$scope.$index].path = $scope.data.path;
        }
        if ($scope.data.files[$scope.$index].status == 'exist' && !$scope.data.files[$scope.$index].overwrite) {
            $scope.data.files[$scope.$index].status = 'skipped';
            $scope.$index++;
            $scope.generateNext();
        } else {
            $scope.data.files[$scope.$index].status = 'processing';
            $http.post(Yii.app.createUrl("/dev/crud/generate"), $scope.data.files[$scope.$index]).success(function (res) {
                if (!!res.touch) {
                    $http.get(res.touch).then(function () {
                        $scope.data.files[$scope.$index].status = res.status;
                        $scope.$index++;
                        $scope.generateNext();
                    });
                } else {
                    $scope.data.files[$scope.$index].status = res.status;
                    $scope.$index++;
                    $scope.generateNext();
                }
            }).error(function (res) {
                $scope.data.files[$scope.$index].status = 'Failed to write';
                $scope.$index++;
                $scope.generateNext();
            });
        }
    } else {
        $scope.step = 5;
        $scope.msg = 'CRUD successfully generated!';
        $scope.msg += ' <a class="btn btn-xs btn-success" target="_blank" href="' + $scope.getControllerUrl() + '">Visit ' + $scope.model.name + ' Now <i class="fa fa-chevron-right"></i></a>';
    }
}