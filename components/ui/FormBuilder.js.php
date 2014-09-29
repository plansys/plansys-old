<?php
ob_start();
?>
<script type="text/javascript">
<?php ob_start(); ?>
    app.controller("<?= $modelClass ?>Controller", function ($scope, $parse, $timeout, $http, $localStorage) {
        $scope.form = <?php echo json_encode($this->form); ?>;
        $scope.model = <?php echo @json_encode($data['data']); ?>;
        $scope.errors = <?php echo @json_encode($data['errors']); ?>;
        $scope.params = <?php echo @json_encode($renderParams); ?>;
<?php if (is_object(Yii::app()->controller) && is_object(Yii::app()->controller->module)): ?>
            $scope.module = '<?= Yii::app()->controller->module->id ?>';
<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('info')): ?>
            $scope.flash = '<?= Yii::app()->user->getFlash('info'); ?>';
<?php endif; ?>
<?php if (isset($data['validators'])): ?>
            $scope.validators = <?php echo @json_encode($data['validators']); ?>;
<?php endif; ?>
<?php if (is_subclass_of($this->model, 'ActiveRecord') && isset($data['isNewRecord'])): ?>
            $scope.isNewRecord = <?php echo $data['isNewRecord'] ? "true" : "false" ?>;
<?php endif; ?>

        $storage = $localStorage;
        $scope.$storage = $storage;

        document.title = $scope.form.title;
        $scope.$watch('form.title', function () {
            document.title = $scope.form.title;
        });
        $scope.formSubmitting = false;
        $scope.form.submit = function (button) {
            $timeout(function () {
                if (typeof button != "undefined") {
                    var baseurl = button.url;
                    if (typeof button.url != 'string' || button.url.trim() == '' || button.url.trim() == '#') {
                        baseurl = '<?= Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                    }

                    var parseParams = $parse(button.urlparams);
                    var urlParams = angular.extend($scope.params, parseParams($scope));

                    var url = Yii.app.createUrl(baseurl, urlParams);

                    $("div[ng-controller=<?= $modelClass ?>Controller] form").attr('action', url).submit();
                }
            }, 0);
        };

        $("div[ng-controller=<?= $modelClass ?>Controller] form").submit(function (e) {
            if ($scope.uploading.length > 0) {
                alert("Mohon tunggu sampai proses file upload selesai.");
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            $scope.formSubmitting = true;
        });

        $scope.form.canGoBack = function () {
            return (document.referrer == "" || window.history.length > 1);
        }

        $scope.form.goBack = function () {
            window.history.back();
        }

        $scope.uploading = [];

        // execute inline JS
        $timeout(function () {
            $("div[ng-controller=<?= $modelClass ?>Controller]").css('opacity', 1);

<?= $inlineJS; ?>
        }, 0);
    });
<?php $script = ob_get_clean(); ?>
</script>
<?php
ob_get_clean();

return $script;

