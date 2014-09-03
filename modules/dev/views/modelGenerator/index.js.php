<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", '$timeout', function($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($models); ?>;
            $scope.active = null;
            $scope.saving = false;
            $scope.activeTree = null;
            $scope.select = function(item){
                $scope.activeTree = item;
                $scope.active = item.$modelValue;
            };
            $scope.is_selected = function(item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
        }
    ]);
</script>