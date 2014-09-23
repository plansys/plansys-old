
app.controller("TodoWidgetController", function ($scope, $http, $timeout, $localStorage) {
    widget = $storage.widget.list.TodoWidget.widget;
    $storage = $localStorage;
    $scope.$storage = $storage;
    $scope.widget = widget;

    $storage.todo = $storage.todo || {
        view: 'active'
    };
    var jsonItems = JSON.parse($("#todo-data").text().trim());
    $storage.todo.items = [];

    for (i in jsonItems) {
        $storage.todo.items.push(jsonItems[i]);
    }


    $scope.prepareTodos = function () {
        var count = 0;
        var i = $storage.todo.items.length;
        while (i--) {
            if ($storage.todo.items[i].note.trim() === "") {
                $storage.todo.items.splice(i, 1);
            } else {
                if ($storage.todo.items[i].status == 0) {
                    count++;
                }
            }
        }

        $storage.todo.items.push({note: "", status: 0, 'type': 'note'});
        widget.badge = count;
    }

    $scope.getStatus = function () {
        return $storage.todo.view == "active" ? 0 : 1;
    }

    $scope.countText = function () {
        var c = $scope.count();
        var a = $storage.todo.view.charAt(0).toUpperCase() + $storage.todo.view.substring(1);

        return c + " " + a + " item" + (c > 1 ? "s" : "");
    }

    $scope.toggleStatus = function (item) {
        item.status = (item.status == 0 ? 1 : 0);
        $scope.updateTodo(item);
    }
    $scope.clear = function (item) {
        $http.post(Yii.app.createUrl('/widget/TodoWidget.clear'), item)
                .success(function (data) {
                    var i = $storage.todo.items.length;
                    while (i--) {
                        if ($storage.todo.items[i].status == 1) {
                            $storage.todo.items.splice(i, 1);
                        }
                    }
                });
    }

    $scope.count = function () {
        var count = 0;

        for (i in $storage.todo.items) {

            if ($storage.todo.items[i] != null && $storage.todo.items[i].note != '') {
                if ($storage.todo.view == 'completed' && $storage.todo.items[i].status == 1) {
                    count++;
                } else if ($storage.todo.view == 'active' && $storage.todo.items[i].status == 0) {
                    count++;
                } else if ($storage.todo.view == 'all') {
                    count++;
                }
            }
        }
        return count;
    }
    $scope.updateTodo = function (item) {
        $http.post(Yii.app.createUrl('/widget/TodoWidget.update'), item)
                .success(function (data) {
                    if (typeof data.id != "undefined") {
                        item.id = data.id;
                    }
                });
    }

    $scope.typeTodo = function (item, e) {
        if (item.note == '') {
            $scope.updateTodo(item);
        }

        $scope.prepareTodos();
    }

    $scope.prepareTodos();

});