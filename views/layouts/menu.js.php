<script>
    app.controller("<?= $class ?>MenuTree", ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
            $scope.list = <?php echo json_encode($list); ?>;
            $scope.active = null;
            $scope.selecting = false;
            $scope.select = function (item) {
                this.toggle();
                $scope.selecting = true;
                $scope.active = item;
            };

            $scope.iconAvailable = function (item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            }
            $scope.isCollapsed = function (item) {
                return item.state == 'collapsed' ? true : false;
            }
            $scope.isSelected = function (item) {
                return angular.equals(item, $scope.active) ? 'active' : '';
            }
        }
    ]);
</script>