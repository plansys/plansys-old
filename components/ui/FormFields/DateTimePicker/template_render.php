    
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
        <data name="date_options" class="hide"><?= json_encode($this->datepickerOptions) ?></data>
        <!-- /data -->

        <!-- field -->
        <!-- date field -->
        <div ng-if="fieldType != 'time'" class="date-field input-group">
            <!-- value -->
            <input type="text" <?= $this->expandAttributes($this->fieldOptions) ?>
                   ng-model="date" ng-change="changeDate(this)" value="<?= $this->value ?>"
                   />

            <!-- btn icon -->
            <span class="input-group-btn">
                <div ng-click="openDatePicker($event)" class="btn btn-sm btn-default">
                    <i class="glyphicon glyphicon-calendar"></i>
                </div>
            </span>
        </div>

        <!-- time field -->
        <div ng-if="fieldType != 'date'" class="time-field">
            <timepicker ng-model="time" ng-change="changeTime(this)" 
                        hour-step="1" minute-step="15" show-meridian="false"></timepicker>
        </div>

        <input id="<?= $this->renderID ?>" name="<?= $this->renderName ?>" type="hidden" ng-value="value"/>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
        <div class="clearfix"></div>
    </div>
</div>