<?php
ob_start();
?>
<script type="text/javascript">
<?php ob_start(); ?>
    app.controller("<?= $modelClass ?>Controller", function($scope, $parse, $timeout) {
        $scope.form = <?php echo json_encode($this->form); ?>;
        $scope.model = <?php echo @json_encode($data['data']); ?>;
        $scope.errors = <?php echo @json_encode($data['errors']); ?>;
        $scope.params = <?php echo @json_encode($renderParams); ?>;
<?php if (isset($data['validators'])): ?>
            $scope.validators = <?php echo @json_encode($data['validators']); ?>;
<?php endif; ?>

        $scope.form.submit = function(button) {
            if (typeof button != "undefined") {
                var baseurl = button.url;
                if (typeof button.url != 'string' || button.url.trim() == '' || button.url.trim() == '#') {
                    baseurl = '<?= Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                }

                var parseParams = $parse(button.urlparams);
                var urlParams = parseParams($scope);

                var url = Yii.app.createUrl(baseurl, urlParams);
                $("div[ng-controller=<?= $modelClass ?>Controller] form").attr('action', url).submit();
            }
        };

        $scope.form.canGoBack = function() {
            return (document.referrer == "" || window.history.length > 1);
        }

        $scope.form.goBack = function() {
            window.history.back();
        }
        
        // execute inline JS
        $timeout(function() {
            $("div[ng-controller=<?= $modelClass ?>Controller]").css('opacity', 1);
            
<?= $inlineJS; ?>
        }, 0);
    });
<?php $script = ob_get_clean(); ?>
</script>
<?php
ob_get_clean();

return $script;

