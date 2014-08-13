

<div drop-down-list <?= $this->expandAttributes($this->options) ?>>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
                <?= $this->label ?>
        </label>
    <?php endif; ?>
    <!-- /label -->

    <div class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="searchable" class="hide" ><?= $this->searchable ?></data>
        <data name="show_other" class="hide" ><?= $this->showOther ?></data>
        <data name="other_label" class="hide" ><?= $this->otherLabel ?></data>
        <data name="model_class" class="hide"><?= @get_class($model) ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list) ?></data>
        <!-- /data -->

        <!-- field -->
        <div class="<?= $this->fieldClass ?>" dropdown on-toggle="toggled(open)">
            <!-- default button -->
            <button ng-if="!showOther || (showOther && itemExist())" type="button" 
                    <?= $this->expandAttributes($this->fieldOptions) ?>>
                <span class="dropdown-text" ng-bind-html="text"></span>
                &nbsp;<span class="caret"></span>
            </button>

            <!-- typeable button -->
            <button ng-if="showOther && !itemExist()" type="button" 
                    class="split-button <?= @$this->fieldOptions['class'] ?>">
                <span class="caret"></span>
            </button>
            <input ng-if="showOther && !itemExist()" type="text" 
                   ng-model="value" ng-change="update(value)" ng-delay="500"
                   class="form-control dropdown-other-type">

            <!-- dropdown item -->
            <div class="dropdown-menu open">
                <div class="search" ng-show="searchable">
                    <input type="text"
                           ng-model="search"
                           ng-change="doSearch()"
                           placeholder="Search ..."
                           class="input-block-level search-dropdown form-control" autocomplete="off">
                </div>
                <ul class="dropdown-menu inner" role="menu">
                    <li class="dropdown-item" ng-show="search == '' || (value + ' ' + text).toLowerCase().indexOf(search.toLowerCase()) > -1"
                        ng-repeat="(value,text) in formList">
                        <a dropdown-toggle href="#" ng-click="update(value);" value="{{value}}">
                            {{ text }}
                        </a>
                    </li>
                    <hr ng-if="showOther != ''" />
                    <li class="dropdown-other" ng-if="showOther != ''">
                        <a dropdown-toggle href="#" ng-click="update(otherLabel);" value="{{itemExist() ? otherLabel : value}}">
                            {{ itemExist() ? otherLabel : value}}
                        </a>                        
                    </li>
                </ul>
            </div>
        </div>

        <input type="text" class="invisible"
               ng-model="value" id="<?= $this->renderID ?>"
               name="<?= $this->name ?>" value='<?= $this->value ?>'/>
        <!-- /field -->

        <!-- error -->
        <?php if (count(@$errors) > 0): ?>
            <div class="alert error alert-danger">
                <?= $errors[0] ?>
            </div>
        <?php endif ?>
        <!-- /error -->
    </div>
</div>