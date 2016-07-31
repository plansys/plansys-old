<div ng-controller="FormBuilderController">
    <style>
        .toolbar .nav-tabs {
            background:#fff !important;
        }
    </style>
    <div ng-if="editor.activeTab.type == 'form'" ui-layout options="{ flow : 'column',dividerSize:1}" style="top:28px;height:auto;">
        <div ui-layout-container>
            <!-- form-builder -->
            <?php include("form_builder_mode.php"); ?>
            <!-- form-builder -->
        </div>
        <div ui-layout-container size='30%' min-size="300px">
            <!-- form-builder-toolbar -->
            <tabset class="toolbar">
                <tab ng-click="editor.activeTab.sidebar.toolbar = true" 
                     ng-if="editor.activeTab.mode == 'layout'"
                     active="editor.activeTab.sidebar.toolbar">
                    <tab-heading>
                        <div style="width:57px;float:left;">
                            <i class="fa fa-bars"></i> Toolbar
                        </div>
                    </tab-heading>
                    <?php include("form_toolbar.php"); ?>
                </tab>
                <tab ng-click="editor.activeTab.sidebar.properties = true" active="editor.activeTab.sidebar.properties">
                    <tab-heading>
                        <div style="width:75px;float:left;">
                            <i class="fa fa-cogs"></i> Properties
                        </div>
                    </tab-heading>
                    <?php include("form_properties.php"); ?>
                </tab>
            </tabset>
            <!-- /form-builder-toolbar -->
        </div>
    </div>
</div>
<script>
    app.controller("FormBuilderController", function ($scope, $http, $timeout, $window, $compile, $localStorage) {
        editor.formBuilder.onload = function () {
            editor.activeTab.active = null;
            var alias = editor.activeTab.alias;
            editor.startLoading(alias);
            editor.activeTab.initTimeout = setTimeout(function () {
                editor.stopLoading(alias);
            }, 2000);
        }
    });
</script>