<script>
    app.controller("<?= $class ?>MenuTree", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            $scope.list = <?php echo json_encode($list); ?>;
            $scope.activeTree = null;
            $scope.active = null;
            $scope.selecting = false;
            $scope.select = function(activeTree) {
                $scope.selecting = true;
                $scope.active = activeTree.$modelValue;
                $scope.activeTree = activeTree;
                
                var item = $scope.active;
                var list = $scope.list;
                var parent = $scope.$parent;
                var form = parent.form;
                var model = parent.model;
                var error = parent.error;
                
                <?= $onclick ?>
                
                $timeout(function() {
                    $scope.selecting = false;
                }, 0);
            };
            $scope.iconAvailable = function(item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            }
            $scope.isCollapsed = function(item) {
                return item.state == 'collapsed' ? true : false;
            }
            $scope.isSelected = function(item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
        }
    ]);
</script>