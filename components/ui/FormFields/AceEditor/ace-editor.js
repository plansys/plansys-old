app.directive('aceEditor', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                var atts = JSON.parse($el.find("data[name=attr]").text());
                $scope.popup = function() {
                    parent.aceEditorScope = $scope;
                    window.PopupCenter(Yii.app.createUrl('/formfield/Text.codePopUp'),'', '1000', '500');
                }
            };
        }
    };
});