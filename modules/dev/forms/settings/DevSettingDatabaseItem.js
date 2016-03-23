$scope.formatName = function(result) {
    result = result.replace(/ /g, '');
    return result.charAt(0).toUpperCase() + result.slice(1);
}
