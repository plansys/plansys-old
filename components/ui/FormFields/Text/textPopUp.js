var parent = window.opener.parentScope;
var active = parent.active;
$scope.code = false;
$scope.parent = parent;
$timeout(function() {
    switch (active.type) {
        case "Text":
            $scope.code = active.value;
            break;
        case "GridView":
             $scope.code = parent.aceEditorScope.item.html;
    }
    window.document.title = "["+active.type + "] Save: Ctrl + S ";
});

$scope.$watch('parent.saving', function(v) {
    if (v) {
        window.document.title = "["+active.type + "] Saving...";
    } else {
        window.document.title = "["+active.type + "] Save: Ctrl + S ";
    }
});

window.$(document).keydown(function (event) {
    window.document.title = "["+active.type + "] Save: Ctrl + S ";
    if (!(String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19)) return true;
    
    
    switch (active.type) {
        case "Text":
            active.value = $scope.code;
            break;
        case "GridView":
            parent.aceEditorScope.item.html = $scope.code;
    }
    parent.save();
    
    event.preventDefault();
    return false;
});