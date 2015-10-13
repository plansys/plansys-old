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

function generateControllerPath() {
    var path = $scope.data.path.split('.');
    path.pop();
    path.pop();
    return path.join(".") + ".controllers";
}

$scope.params.prefix = generateClassPrefix(activeScope.activeItem.alias);
$scope.params.alias = activeScope.activeItem.alias;
$scope.data = {};
$scope.step = '1';
$scope.msg = '';
$scope.onNameChange = function () {
    $scope.model.name = ($scope.model.name.charAt(0).toUpperCase() + $scope.model.name.slice(1)).replace(/[^a-z0-9]/gi, '');
    $scope.model.dirName = ($scope.model.name.charAt(0).toLowerCase() + $scope.model.name.slice(1)).replace(/[^a-z0-9]/gi, '');
    $scope.data = {
        path: trim($scope.params.alias, '.') + '.' + $scope.model.dirName,
        files: []
    };
}
$scope.form.submit = function (f) {

    // main form data
    $scope.data.files.push({
        name: $scope.data.path,
        type: 'folder'
    });
    $scope.data.files.push({
        name: $scope.params.prefix + $scope.model.name + 'Index.php',
        className: $scope.params.prefix + $scope.model.name + 'Index',
        type: 'index',
    });
    $scope.data.files.push({
        name: $scope.params.prefix + $scope.model.name + 'Form.php',
        className: $scope.params.prefix + $scope.model.name + 'Form',
        type: 'form',
    });
    $scope.data.files.push({
        name: $scope.model.name + 'Controller.php',
        className: $scope.model.name + 'Controller',
        type: 'controller',
        path: generateControllerPath()
    });

    $scope.step = 2;
    $scope.msg = 'Cheking file availability...';
    $scope.resetCheck();
    $scope.checkNext();
}
$scope.resetCheck = function () {
    $scope.$check = 0;
}
$scope.checkNext = function () {
    $scope.data.files[$scope.$check].status = 'ready';
    $scope.$check++;

    if ($scope.$check < $scope.data.files.length) {
        $scope.checkNext();
    } else {
        $scope.step = 3;
    }
}