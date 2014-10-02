$scope.loading = false;

$scope.status = "Ready";

if ($scope.model.done.length != $scope.model.migrations.length) {
    $scope.status += "<br/> Please migrate all first before making new migration.";
}

$scope.migration = $scope.model.isNew;
$scope.migrated = {};

$scope.toggleMigration = function () {
    $scope.migration = !$scope.migration;
}

$scope.runInternal = function (id, file, store, f) {
    $scope.loading = true;
    if (file != null) {
        $scope.status = "Executing: #" + id + " - " + file + " ...";
    } else {
        $scope.status = "Executing: #" + id + " ...";
    }

    $http.get(Yii.app.createUrl('/migration/run', {
        id: id,
        file: file,
        store: 1
    })).success(function (data) {
        $scope.loading = false;
        if (data == "") {
            if (file != null) {
                $scope.status = "Migration: #" + id + " - " + file + " Successfully executed!";

                if ($scope.model.done.indexOf(id + "_" + file) < 0) {
                    $scope.model.done.push(id + "_" + file);
                }
            } else {
                $scope.status = "Migration: #" + id + " Successfully executed!";
            }

            if (typeof f == "function") {
                f(id, file);
            }
        } else {
            $scope.errors = {'newsql': [data]};
            $scope.errorIdx = {
                id: id,
                file: file
            }
        }
    });
}

$scope.errorIdx = {
    id: -1,
    file: ''
};

$scope.isMigrated = function () {
    var length = 0;
    for (k in $scope.model.migrations) {
        for (i in $scope.model.migrations[k].items) {
            if (i != "$$hashKey") {
                length++;
            }
        }
    }
    return length == $scope.model.done.length;
}

$scope.panelClass = function (m) {
    if ($scope.errorIdx.id == m.id) {
        return 'danger';
    }

    if (typeof $scope.migrated[m.id] != "undefined") {
        return 'success';
    }

    var doneAll = true;
    for (i in m.items) {
        if ($scope.model.done.indexOf(m.id + "_" + i) < 0) {
            doneAll = false;
        }
    }

    return (doneAll ? "success" : "default");
}

$scope.fileClass = function (m, file) {
    if ($scope.errorIdx.id == m.id && $scope.errorIdx.file == file) {
        return 'migration-file panel panel-danger';
    }

    if ($scope.model.done.indexOf(m.id + "_" + file) >= 0) {
        return 'migration-file panel panel-success';
    } else {
        return 'migration-file panel panel-default';
    }
}

$scope.migrateAll = function (e) {
    e.stopPropagation();
    e.preventDefault();

    $scope.migAll = [];
    for (i in $scope.model.migrations) {
        var migration = $scope.model.migrations[i];

        if (migration.id > $scope.model.idx) {
            for (k in migration.items) {
                if ($scope.model.done.indexOf(migration.id + "_" + k) < 0) {
                    var m = migration[k];
                    $scope.migAll.unshift(migration.id + "|" + k);
                }
            }
        }
    }

    $scope.migratingId = false;
    $scope.runMigration();
}
$scope.migratingId = false;
$scope.migrateId = function (e, id) {
    e.stopPropagation();
    e.preventDefault();

    $scope.migAll = [];
    for (i in $scope.model.migrations) {
        var migration = $scope.model.migrations[i];
        if (migration.id === id) {
            for (k in migration.items) {
                var m = migration[k];
                $scope.migAll.unshift(migration.id + "|" + k);
            }
        }
    }
    $scope.migratingId = true;
    $scope.runMigration();
}

$scope.runMigration = function (id, file) {
    if (typeof id != "undefined") {
        $scope.migrated[id] = $scope.migrated[id] || {};
        $scope.migrated[id][file] = $scope.migrated[id][file] || {};
    }

    if ($scope.migAll.length > 0) {
        var item = $scope.migAll.shift().split("|");
        $scope.runInternal(item[0], item[1], true, $scope.runMigration);
    } else {
        $scope.migration = false;
        $scope.migratingId = false;
    }
}

$scope.run = function (e, id, file) {
    e.stopPropagation();
    e.preventDefault();

    $scope.runInternal(id, file, 0, function () {
        $scope.migration = false;
    });
}