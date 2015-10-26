if (!window.opener || !window.opener.activeScope) {
    window.close();
}
var activeScope = window.opener.activeScope;
function initClassPrefix(s) {
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

$scope.classPrefix = null;
$scope.generateClassPrefix = function () {
    $scope.model.prefix = "";
    var prefixList = $scope.model.model.replace(/([a-z](?=[A-Z]))/g, '$1 ').split(" ");

    if (prefixList.length > 1) {
        $scope.classPrefix = {'': '-- NONE --', '---': '---'};
        $scope.classPrefix[prefixList[0]] = prefixList[0];
    } else {
        $scope.classPrefix = null;
    }
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

$scope.getStatus = function (f) {
    if (!f || !f.status)
        return "Waiting";

    if (f.status == 'exist') {
        return f.status;
    }

    return f.status;
}
$scope.getControllerUrl = function (action) {
    var module = '';
    var mp = $scope.getModulePath().split(".");
    if ($scope.getModulePath().length > 3) {
        module = mp.pop() + "/";
    }
    action = action || "/index";

    if (!!$scope.model) {
        return Yii.app.createUrl(module + $scope.model.lcName.substr($scope.model.prefix.length) + action);
    }
}
$scope.getControllerPath = function () {
    return $scope.getModulePath() + ".controllers";
}
$scope.params.prefix = initClassPrefix(activeScope.activeItem.alias);
$scope.params.alias = activeScope.activeItem.alias;
$scope.data = {};
$scope.exists = [];
$scope.step = '1';
$scope.msg = '';
$scope.relationList = [];
$scope.relNameList = {};

$scope.resetData = function () {
    $scope.data = {
        path: trim($scope.params.alias, '.') + '.' + $scope.model.lcName.substr($scope.model.prefix.length),
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

    $scope.generateClassPrefix();

    $http.get(Yii.app.createUrl('/dev/crud/listRelation&m=' + $scope.model.name))
        .success(function (res) {
            $scope.relationList = res;
            $scope.relNameList = {
                '': 'Choose Relation',
                '---': '---'
            };
            for (i in res) {
                $scope.relNameList[i] = i;
            }
        });
}
$scope.onPrefixChange = function() {
    $timeout(function() {
        $scope.resetData();
    });
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
    var modelName = $scope.model.name;
    if ($scope.model.prefix != '') {
        modelName = modelName.substr($scope.model.prefix.length);
    }

    if ($scope.model.masterData == 'No') {
        
        for (i in $scope.model.relations) {
            var rel = $scope.model.relations[i];
            if (rel.formType == "SubForm") {
                rel.subFormClass = $scope.params.prefix + modelName + ucfirst(rel.name) + 'Subform';
            }
        }
        
        $scope.data.files.push({
            name: $scope.params.prefix + modelName + 'Index.php',
            className: $scope.params.prefix + modelName + 'Index',
            extendsName: $scope.model.model,
            type: 'index',
            bulkCheckbox: $scope.model.bulkCheckbox
        });
        $scope.data.files.push({
            name: $scope.params.prefix + modelName + 'Form.php',
            className: $scope.params.prefix + modelName + 'Form',
            extendsName: $scope.model.model,
            type: 'form',
            relations: $scope.model.relations
        });
        $scope.data.files.push({
            name: modelName + 'Controller.php',
            className: modelName + 'Controller',
            type: 'controller',
            mode: 'crud',
            formName: $scope.params.prefix + modelName + 'Form',
            indexName: $scope.params.prefix + modelName + 'Index',
            alias: $scope.data.path,
            bulkCheckbox: $scope.model.bulkCheckbox,
            path: $scope.getControllerPath(),
            relations: $scope.model.relations
        });

        for (i in $scope.model.relations) {
            var rel = $scope.model.relations[i];
            switch (rel.type) {
                case "CBelongsToRelation":
                    if (rel.formType =='PopUp') {
                        $scope.data.files.push({
                            name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Relform.php',
                            className: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Relform',
                            extendsName: rel.className,
                            type: 'relform',
                            relation: rel
                        });
                    } else if (rel.formType == 'SubForm') {
                        $scope.data.files.push({
                            name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Subform.php',
                            className: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Subform',
                            extendsName: rel.className,
                            type: 'subform',
                            relation: rel
                        });
                    }
                    break
                case "CHasManyRelation":
                case "CManyManyRelation":
                    if (rel.formType == "SubForm") {
                        $scope.data.files.push({
                            name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Subform.php',
                            className: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Subform',
                            extendsName: rel.className,
                            type: 'subform',
                            relation: rel
                        });
                    } else if (rel.formType == "Table") {
                        if (rel.editable == "PopUp" || rel.insertable == "PopUp") {
                            $scope.data.files.push({
                                name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Relform.php',
                                className: $scope.params.prefix + modelName + ucfirst(rel.name) + 'Relform',
                                extendsName: rel.className,
                                type: 'relform',
                                relation: rel
                            });
                        }
    
                        if (rel.chooseable == 'Yes') {
                            $scope.data.files.push({
                                name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'ChooseRelform.php',
                                className: $scope.params.prefix + modelName + ucfirst(rel.name) + 'ChooseRelform',
                                extendsName: rel.className,
                                type: 'chooserelform',
                                relation: rel,
                                inlineJs: $scope.params.prefix + modelName + ucfirst(rel.name) + 'ChooseRelform.js'
                            });
                            $scope.data.files.push({
                                name: $scope.params.prefix + modelName + ucfirst(rel.name) + 'ChooseRelform.js',
                                template: 'TplRelMMIndex',
                                replace: {
                                    dsParent: 'ds' + ucfirst(rel.name)
                                },
                                type: 'js',
                                relation: rel
                            });
                        }
                    }
                    break;
            }
        }
    } else {
        $scope.data.files.push({
            name: $scope.params.prefix + modelName + 'Master.php',
            className: $scope.params.prefix + modelName + 'Master',
            extendsName: $scope.model.model,
            type: 'master',
        });
        $scope.data.files.push({
            name: modelName + 'Controller.php',
            className: modelName + 'Controller',
            indexName: $scope.params.prefix + modelName + 'Master',
            type: 'controller',
            mode: 'master',
            alias: $scope.data.path,
            path: $scope.getControllerPath()
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
    // window.opener.location.reload();
}
$scope.generateNext = function () {
    if ($scope.step < 4) {
        $scope.msg = 'Generating Files...';
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
                        $http.get(Yii.app.createUrl("/dev/crud/warning", {c: $scope.data.files[$scope.$index].className}))
                            .success(function (res) {
                                $scope.data.files[$scope.$index].warning = res;
                                $scope.$index++;
                                $scope.generateNext();
                            });
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
        $scope.msg += ' <a class="btn btn-xs btn-success" target="_blank" onClick="window.close()" href="' + $scope.getControllerUrl() + '">Visit ' + $scope.model.name + ' Now <i class="fa fa-chevron-right"></i></a>';
    }
}