<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", '$timeout', function ($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($controllers); ?>;
            $scope.active = null;
            $scope.saving = false;
            $scope.activeTree = null;
            $scope.select = function (item) {
                $scope.activeTree = item;
                $scope.active = item.$modelValue;
            };
            $scope.is_selected = function (item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
            $scope.formatName = function (name) {
                return name.substr(0, name.length - 10);
            }
        }
    ]);
</script>