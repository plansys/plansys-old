
<script>
    app.controller("<?= $class ?>MenuTree", ["$scope", "$http", "$timeout", "$templateCache", function ($scope, $http, $timeout, $templateCache) {
            $scope.list = <?= json_encode($list); ?>;
            $scope.active = null;
            $scope.sections = <?= json_encode($sections); ?>;
            $scope.selecting = false;
            $scope.targetSection = null;
            $scope.targetHTML = '';
            $scope.select = function (item) {
                this.toggle();
                item.state = '';
                $scope.selecting = true;
                $scope.active = item;
            };
            $scope.getUrl = function (item) {
                return item.url || '#';
            };
            $scope.getTarget = function (item) {
                if (!!$scope.sections[item.target]) {
                    return '_self';
                }
                return item.target || '_self';
            };
            $scope.iconAvailable = function (item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            };
            $scope.isSelected = function (item) {
                return angular.equals(item, $scope.active) ? 'active' : '';
            };
        }
    ]);
</script>