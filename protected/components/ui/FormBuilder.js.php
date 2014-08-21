<?php
ob_start();
?>
<script type="text/javascript">
<?php ob_start(); ?>
    app.controller("<?= $modelClass ?>Controller", function($scope, $parse) {
        $scope.form = <?php echo json_encode($this->form); ?>;
        $scope.model = <?php echo @json_encode($data['data']); ?>;
        $scope.errors = <?php echo @json_encode($data['errors']); ?>;
        $scope.<?= $modelClass ?> = $scope;

        $scope.form.submit = function(button) {
            if (typeof button != "undefined") {
                var baseurl = button.url;
                if (typeof button.url != 'string' || button.url.trim() == '' || button.url.trim() == '#') {
                    baseurl = '<?= Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                }

                var parseParams = $parse(button.urlparams);
                var urlParams = parseParams($scope);

                var url = Yii.app.createUrl(baseurl, urlParams);
                $("form[ng-controller=<?= $modelClass ?>Controller]").attr('action', url).submit();
            }
        };
    }
    );
<?php $script = ob_get_clean(); ?>
</script>
<?php
ob_get_clean();

return $script;

