<?php
ob_start();
?>
<script type="text/javascript">
<?php ob_start(); ?>
    app.controller("<?= $this->ctrlName ?>Controller", function ($scope, $parse, $timeout, $http, $localStorage) {

<?= $inlineJS; ?>
    });
    registerController("<?= $this->ctrlName ?>Controller");
<?php $script = ob_get_clean(); ?>
</script>
<?php
ob_get_clean();

return $script;
