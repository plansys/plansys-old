
<div ps-action-bar class="action-bar-container">
    <div class="action-bar" >
        <div class="title-bar">
            <span class="title"><?= $this->title ?></span>
        </div>
        <?php if (Yii::app()->controller->module->id != "sys" && $this->form['layout']['name'] == 'full-width'): ?>
            <div class="print-bar">
                <div class="ac-portlet-btngroup btn-group" dropdown>
                    <button type="button" class="btn ac-portlet-button btn-sm btn-default dropdown-toggle">
                        <i class="fa fa-bars fa-nm"></i> 
                    </button>
                    <ul class="ac-portlet-menu dropdown-menu pull-right" role="menu">
                        <?php if (!Yii::app()->user->isGuest): ?>
                            <li>
                                <a target="_blank" href="{{ Yii.app.createUrl('/sys/auditTrail/view', {key: pageInfo.key})}}">
                                    <i class="fa fa-newspaper-o fa-lg fa-fw" style='margin:0px 5px 0px -10px;'></i> 
                                    Audit Trail
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="#" dropdown-toggle class='ac-print'>
                                <i class="fa fa-print fa-lg  fa-fw" style='margin:0px 5px 0px -10px;'></i> Print Page
                            </a>
                        </li> 
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($this->form['layout']['name'] == 'dashboard'): ?>
            <div class="data hide" name="portlets"><?= json_encode($this->portlets); ?></div>
            <div class="print-bar">
                <div class="ac-portlet-btngroup btn-group" dropdown>
                    <button type="button" class="btn ac-portlet-button btn-sm btn-default dropdown-toggle">
                        <i class="fa fa-bars fa-nm"></i> 
                        <span class="caret"></span>
                    </button>
                    <ul class="ac-portlet-menu dropdown-menu pull-right" role="menu" >
                        <li class="ac-portlet-list" ng-repeat="portlet in portlets">
                            <a href="#" ng-click="togglePortlet(portlet, $event)">
                                <i class="fa fa-check-square-o fa-lg fa-fw" ng-if="!portlet.hide" ></i>
                                <i class="fa fa-square-o fa-lg fa-fw" ng-if="portlet.hide" ></i>
                                {{ !!portlet.title && portlet.title != '' ? portlet.title : portlet.name }}
                            </a>
                        </li>
                        <?php if ($this->dashboardMode == "view"): ?>
                            <hr/>
                            <li dropdown-toggle>
                                <a href="#" ng-click="resetDashboard()">
                                    <i class="fa fa-flag-checkered fa-nm"></i> Reset 
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <div class="link-bar">
            <div ng-show='!formSubmitting'>
                <?= $this->renderLinkBar ?>
            </div>

            <div ng-show='formSubmitting'>
                <i class="fa fa-spin fa-refresh fa-lg" style='margin:10px 10px 0px 0px'></i>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php if ($this->showSectionTab == "Yes"): ?>
            <div class="action-tab" >
                <a href="#<?= strtolower(preg_replace('/[^\da-z]/i', '_', $this->firstTabName)) ?>" top="0" class="active"><?= $this->firstTabName ?></a>
                <div class="clearfix"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="<?= strtolower(preg_replace('/[^\da-z]/i', '_', $this->firstTabName)) ?>"></div>