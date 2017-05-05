app.directive('uploadFileNative', function($timeout, $http) {
     return {
          require: '?ngModel',
          scope: true,
          compile: function(element, attrs, transclude) {
               if (attrs.ngModel && !attrs.ngDelay) {
                    attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
               }
                    
               return function($scope, $el, attrs, ctrl) {
                    $scope.name = $el.attr('ps-name');
                    $scope.files = {};
                    $scope.values = [];
                    
                    if (ctrl) {
                         $($el).change(function() {
                              $fel = $el[0].files;
                              for (var i = 0; i < $fel.length; i++) {
                                   $scope.files[$fel[i].name] = $fel[i];
                                   $scope.values.push($fel[i]);
                              }
                              
                              ctrl.$setViewValue($scope.values);
                         })
                         
                         ctrl.$render = function() {
                              if (!ctrl.$viewValue) {
                                   $scope.values = [];
                                   $el[0].value = "";
                              }
                         }
                    }
                    
                    var parent = $scope.getParent($scope);
                    parent[$scope.name] = $scope;
               }
          }
     };
});