<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">

        <div size='69%' min-size="300px">
            <!-- form-builder-content -->
            <div class="form-builder-saving">
                <span ng-show='saving'> 
                    <i class="fa fa-refresh fa-spin"></i>
                    Saving... 
                </span>

                <span ng-show='!saving && layoutChanging'>
                    <i class="fa fa-refresh fa-spin"></i>
                    Rendering Layout...
                </span>
            </div>
            <script type="text/ng-template" id="FormTree"><?php include('form_fields.php'); ?></script>
            <div ng-include="Yii.app.createUrl('dev/forms/renderBuilder', {
                        class: '<?= $class ?>',
                        layout: form.layout.name,
                        cb: cacheBuster
                    })" onload='layoutChanging = false'> 
            </div>
            <!-- /form-builder-content -->
        </div>

        <div min-size="250px">
            <!-- form-builder-toolbar -->
            <tabset class="toolbar">
                <div ng-show="!layoutChanging" 
                     ng-click="cacheBusterChange();"
                     class="pull-right btn btn-xs" style="margin:2px;">
                    <i class="fa fa-refresh fa-nm"></i>
                </div>
                <tab ng-click="tabs.toolbar = true" active="tabs.toolbar" ng-controller="ToolbarController">
                    <tab-heading>
                        <i class="fa fa-bars"></i> Toolbar
                    </tab-heading>
                    <?php include("form_toolbar.php"); ?>
                </tab>
                <tab ng-click="tabs.properties = true" active="tabs.properties">
                    <tab-heading>
                        <i class="fa fa-cogs"></i> Properties
                    </tab-heading>
                    <div class="properties-body form-builder-properties">


                        <div ng-if="active" class="properties-header">
                            <div class='btn btn-danger btn-xs pull-right' 
                                 ng-click='deleteField()'>
                                <i class='fa fa-times'></i>
                                Delete
                            </div>

                            <div class="toolbar-type btn-group" dropdown on-toggle="openToolbarType(open)">
                                <button type="button" class="btn btn-xs btn-default dropdown-toggle change-type">
                                    <i ng-class="toolbarSettings['icon'][active.type]"></i>&nbsp; {{active.type}}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:160px;max-height:200px;" role="menu">
                                    
                                    <li ng-repeat="(name, icon) in toolbarSettings['icon']">
                                        <a href="#" dropdown-toggle value="{{name}}" 
                                           ng-click="active.type = name">
                                            <i class="{{icon}}"></i> {{name}}
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <form class="form-horizontal" role="form" 
                              ng-if="active == null && layout == null">
                                  <?php
                                  if ($formType == "ActiveRecord" || $formType == "Form"):
                                      include('form_properties.php');
                                  else:
                                      echo '<br /><br /><br /><center>&mdash; Welcome '
                                      . 'To Form Builder &mdash;</center>';
                                  endif;
                                  ?>
                        </form>

                        <form class="form-horizontal" role="form" 
                              ng-if=" active == null && layout != null">
                                  <?php
                                  if ($formType == "ActiveRecord" || $formType == "Form"):
                                      include('form_layout.php');
                                  else:
                                      echo '<br /><br /><br /><center>&mdash; Welcome '
                                      . 'To Form Builder &mdash;</center>';
                                  endif;
                                  ?>
                        </form>

                        <div ui-content style="padding:6px 5px 0px 10px;margin-top:3px;"
                             ng-if="active != null">
                            <form id="toolbar-properties" class="form-horizontal" role="form"
                                  ng-if="!typeChanging && active != null"
                                  ng-include="Yii.app.createUrl('dev/forms/renderProperties', {
                                                  class: active.type
                                              })" onload="selected()">
                            </form>
                        </div>

                    </div>
                </tab>
            </tabset>
            <!-- /form-builder-toolbar -->
        </div>
    </div>
</div>
<?php include("form.js.php"); ?>