    
<div date-time-picker <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->name; ?>">
                <?= $this->label ?> <?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>" >
        <!-- data -->
        <data name="name" class="hide"><?= $this->name ?></data>
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="field_type" class="hide"><?= $this->fieldType ?></data>
        <data name="default_today" class="hide"><?= $this->defaultToday ?></data>
        <data name="date_options" class="hide"><?= json_encode($this->datepickerOptions) ?></data>
        <data name="is_disabled" class="hide"><?php
            if (isset($this->fieldOptions['disabled'])) {
                echo $this->fieldOptions['disabled'];
            } else if (isset($this->fieldOptions['ng-disabled'])) {
                echo $this->fieldOptions['ng-disabled'];
            }
        ?></data>
        <!-- /data -->

        <!-- field -->
        <div ng-if="['monthyear', 'date'].indexOf(fieldType) >= 0" style="padding-top:5px;">
            <select ng-disabled="isDPDisabled" ng-options="item for item in dayList" ng-show="fieldType == 'date'"
                    name="<?= $this->renderName ?>[day]" id="<?= $this->renderID ?>_day"
                    ng-model="dd.day" ng-change="<?= @$this->options['ng-change']; ?>;changeDropdown()"></select>&nbsp;<select ng-disabled="isDPDisabled" ng-options="item.i as item.n for item in monthList"
                    name="<?= $this->renderName ?>[month]" id="<?= $this->renderID ?>_month"
                    ng-model="dd.month" ng-change="<?= @$this->options['ng-change']; ?>;changeDropdown()"></select>&nbsp;<select ng-disabled="isDPDisabled" ng-options="item for item in yearList"
                    name="<?= $this->renderName ?>[year]" id="<?= $this->renderID ?>_year"
                    ng-model="dd.year" ng-change="<?= @$this->options['ng-change']; ?>;changeDropdown()"></select>
        </div>

        <!-- date field -->
        <div ng-if="['datepicker', 'datetime'].indexOf(fieldType) >= 0" 
             class="date-field {{ !isDPDisabled ? 'input-group' : ''}}"
             style="{{ !isDPDisabled ? 'text-align:left !important;width:90px;' : '' }}">
            <!-- value -->
            <input type="text" <?= $this->expandAttributes($this->fieldOptions) ?>
                   style="{{ !isDPDisabled ? 'text-align:left !important;width:90px;color:#000;' : ''}}"
                   ng-model="date" ng-change="changeDate(this)" value="<?= $this->value ?>"
                   />

                <!-- btn icon -->
                <span class="input-group-btn" ng-if='!isDPDisabled'>
                    <div ng-click="openDatePicker($event)" class="btn btn-sm btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </span>
        </div>

        <!-- time field -->
        <div ng-if="['time', 'datetime'].indexOf(fieldType) >= 0" class="time-field">
            <timepicker ng-model="time" 
                        readonly-input="$eval(disabledCondition)"
                        ng-change="changeTime(this)" 
                        hour-step="1" minute-step="15" show-meridian="false"></timepicker>
        </div>

        <input id="<?= $this->renderID ?>" name="<?= $this->renderName ?>" type="hidden" ng-value="value"/>
        <!-- /field -->

        <!-- error -->
        <div ng-if="errors[name]"
             style="border-top:0px;border-radius:4px;margin-top:1px;"
             class="alert error alert-danger">
            {{ errors[name][0] }}
        </div>
        <!-- /error -->
        <div class="clearfix"></div>
    </div>
</div>