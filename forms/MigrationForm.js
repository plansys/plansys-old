$scope.loading = false;

if ($scope.model.idx > 0) {
    $scope.status = "Last Migration #" + $scope.model.idx;
} else {
    $scope.status = "No Migration";
}

if ($scope.model.idx != $scope.model.migrations.length) {
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
$scope.panelClass = function (m) {
    if ($scope.errorIdx.id == m.id) {
        return 'danger';
    }


    if (typeof $scope.migrated[m.id] != "undefined") {
        return 'success';
    }

    if ($scope.model.idx < m.id) {
        return 'default';
    }
    else {
        return 'success';
    }
}

$scope.fileClass = function (m, file) {
    if ($scope.errorIdx.id == m.id && $scope.errorIdx.file == file) {
        return 'migration-file panel panel-danger';
    }

    if (typeof $scope.migrated[m.id] !== "undefined" && typeof $scope.migrated[m.id][file] !== "undefined") {
        return 'migration-file panel panel-success';
    } else {
        return 'migration-file panel panel-info';
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
                var m = migration[k];
                $scope.migAll.unshift(migration.id + "|" + k);
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

        if (!$scope.migratingId) {
            $scope.model.idx = id;
        }
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