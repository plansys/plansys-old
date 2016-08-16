<?php Yii::import('application.components.utility.Asset'); ?> 
<script>
    var actionFormList = <?= $this->actionFormList() ?>;
    var editor = {
        formBuilder: {types: {}},
        modelBuilder: {models: {}}
    };
    window.csrf = {
        name: "<?php echo Yii::app()->request->csrfTokenName; ?>",
        token: "<?php echo Yii::app()->request->csrfToken; ?>"
    };
    
    var builder = {
        tab: {
            init: function() {},
            load: function() {},
            select: function() {},
            active: null,
            data: [],
        },
        tree: {
            init: function() {},
            form: {
                init: function() {},
                load: function() {},
                data: [],
            },
            model: {
                init: function() {},
                load: function() {}
            }
        },
        editor: {
            activate: function() {},
            form: {
                init: function() {},
            },
            model: {
                init: function() {},
            }
        },
    };
</script>

<div ng-controller="IndexController">
    <div ui-layout options="{ flow : 'column',dividerSize:1}">
        <div ui-layout-container size='20%' min-size="200px" class="sidebar">
            <?php include("form_builder_tree.php"); ?> 
        </div>
        <div ui-layout-container size='80%'>
            <?php include("index_tabs.php"); ?>
        </div>
    </div>
</div>

<script>
    app.controller("IndexController", function ($scope, $http, $localStorage, $timeout) {
        
    });
</script>
