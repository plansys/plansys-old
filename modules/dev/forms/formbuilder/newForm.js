var activeScope = window.opener.activeScope;
//$scope.params.item = activeScope.activeItem;
function generateClassPrefix(s) {
    var parts = s.split(".");

    console.log(parts);
    if (parts[1] == "forms") {
        var module = parts.shift();
        parts = parts.splice(0);
        parts.unshift(module);
        parts = parts.join(" ").trim().replace(/\s+/g, '.');
    } else if (parts[1] == "modules") {
        parts = parts.splice(2);
        var module = parts.shift();
        parts = parts.splice(1);
        parts.unshift(module);
        parts = parts.join(" ").trim().replace(/\s+/g, '.');
    }

    var result = parts.replace(/(\.\w)/g, function (m) {
        return m[1].toUpperCase();
    });
    result = result.charAt(0).toUpperCase() + result.slice(1);
    
    if (result.indexOf('AppForms') === 0) {
        result = result.replace('AppForms','App');
    }
    
    return result;
}

$scope.params.prefix = generateClassPrefix(activeScope.activeItem.alias);
$scope.onFormNameChange = function () {
    $scope.model.formName = ($scope.model.formName.charAt(0).toUpperCase() + $scope.model.formName.slice(1)).replace(/[^a-z0-9]/gi,'');
}

$scope.form.submit = function (f) {
    if ($scope.model.formName != '') {
        var baseClass = $scope.model.baseClass;
        if (baseClass == '--model--') {
            baseClass = $scope.model.modelName;
        } else if (baseClass == '--custom--') {
            baseClass = $scope.model.customClassName;
        }
        activeScope.addForm($scope.params.prefix + $scope.model.formName, baseClass, activeScope.activeItem);
        window.close();
    }
};

$timeout(function () {
    $("#DevFormNewForm_3").focus();
}, 100);