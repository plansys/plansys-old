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
$scope.params.alias = activeScope.activeItem.alias;
$scope.onNameChange = function () {
    $scope.model.name = ($scope.model.name.charAt(0).toLowerCase() + $scope.model.name.slice(1)).replace(/[^a-z0-9]/gi,'');
}

$scope.form.submit = function (f) {
    $scope.debug = f;
}