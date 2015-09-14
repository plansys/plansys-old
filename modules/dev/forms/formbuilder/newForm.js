var activeScope = window.opener.activeScope;
//$scope.params.item = activeScope.activeItem;
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

$scope.params.prefix = generateClassPrefix(activeScope.activeItem.alias);
$scope.onFormNameChange = function () {
    $scope.model.formName = $scope.model.formName.charAt(0).toUpperCase() + $scope.model.formName.slice(1);
}

$scope.form.submit = function (f) {
    if ($scope.model.formName != '') {
        var baseClass = $scope.model.baseClass;
        if (baseClass == '--model--') {
            baseClass = $scope.model.model;
        } else if (baseClass == '--custom--') {
            baseClass = $scope.model.custom;
        }
        activeScope.addForm($scope.params.prefix + $scope.model.formName, baseClass, activeScope.activeItem);
        window.close();
    }
};