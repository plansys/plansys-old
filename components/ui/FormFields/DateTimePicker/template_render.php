    
<div date-time-picker <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" >
        <!-- data -->
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="field_type" class="hide"><?= $this->fieldType ?></data>
        <data name="default_today" class="hide"><?= $this->defaultToday ?></data>
        <data name="date_options" class="hide"><?= json_encode($this->datepickerOptions) ?></data>
        <!-- /data -->

        <!-- field -->
        <!-- date field -->
        <div ng-if="['date', 'datetime'].indexOf(fieldType) >= 0" 
             class="date-field <?php if (!@$this->fieldOptions['disabled']): ?>input-group<?php endif; ?>"
             <?php if (!!@$this->fieldOptions['disabled']): ?>style="text-align:left !important;width:90px;"<?php endif; ?>
             >
            <!-- value -->
            <input type="text" <?= $this->expandAttributes($this->fieldOptions) ?>
                   <?php if (!!@$this->fieldOptions['disabled']): ?>style="text-align:left !important;width:90px;color:#000;"<?php endif; ?>
                   ng-model="date" ng-change="changeDate(this)" value="<?= $this->value ?>"
                   />

            <?php if (!@$this->fieldOptions['disabled']): ?>
                <!-- btn icon -->
                <span class="input-group-btn" >
                    <div ng-click="openDatePicker($event)" class="btn btn-sm btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </span>
            <?php endif; ?>
        </div>

        <!-- time field -->
        <div ng-if="['time', 'datetime'].indexOf(fieldType) >= 0" class="time-field">
            <timepicker ng-model="time" 
                        <?php if (!!@$this->fieldOptions['disabled']): ?>readonly-input="true"<?php endif; ?> 
                        ng-change="changeTime(this)" 
                        hour-step="1" minute-step="15" show-meridian="false"></timepicker>
        </div>

        <!-- Month Year -->
        <div ng-if="['monthyear'].indexOf(fieldType) >= 0"  >
            <div class="btn-group" dropdown>
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" ng-disabled="disabled">
                    {{ monthList[month]}} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" dropdown-toggle role="menu">
                    <li ng-repeat="i in monthList"><a href="#" ng-click="changeMonth($index)">{{ i}}</a></li>
                </ul>
            </div>
            <div class="btn-group" dropdown>
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" ng-disabled="disabled">
                    {{ year}} <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" dropdown-toggle role="menu">
                    <li ng-repeat="i in yearList"><a href="#" ng-click="changeYear(i)">{{ i}}</a></li>
                </ul>
            </div>

        </div>


        <input id="<?= $this->renderID ?>" name="<?= $this->renderName ?>" type="hidden" ng-value="value"/>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger" style="border-top:0px;border-radius:4px;margin-top:1px;">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
        <div class="clearfix"></div>
    </div>
</div>