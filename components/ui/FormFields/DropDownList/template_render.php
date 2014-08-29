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
        <div class="<?= $this->fieldClass ?>" 
             ng-keydown="dropdownKeypress($event)"
             is-open="isOpen"
             dropdown on-toggle="toggled(open)">

            <!-- default button -->
            <button 
                ng-if="!showOther || (showOther && itemExist())" type="button" 
                <?= $this->expandAttributes($this->fieldOptions) ?> >
                <span class="caret pull-right"></span>
                <span class="dropdown-text" ng-bind-html="text"></span>
            </button>

            <!-- typeable button -->
            <button ng-if="showOther && !itemExist()" tabindex="1" type="button" 
                    style="padding:2px 0px 8px 0px;width:30px; text-align:center;"
                    class="split-button <?= @$this->fieldOptions['class'] ?>">
                <span class="caret" style="float:none;"></span>
            </button>
            <input ng-if="showOther && !itemExist()" type="text"
                   ng-model="value" ng-change="updateOther(value)" ng-delay="500"
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
                    <li ng-repeat-start="item in renderedFormList track by $index" 
                        ng-if="item.value != '---'" class="dropdown-item" 
                        ng-class="{'dropdown-header': isObject(item.value)}"
                        ng-show="isFound(item.value + ' ' + item.key)">

                        <a ng-if="!isObject(item.value)"
                           dropdown-toggle href="#" 
                           ng-click="update(item.key);"
                           value="{{item.key}}">
                            {{ item.value}}
                        </a>
                        <div ng-if="isObject(item.value)" class="dropdown-menu-submenu">
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-line"></div>
                                <div class="dropdown-menu-header-text">{{item.key}}</div>
                            </div>
                            <ul class="dropdown-menu inner" role="menu" 
                                style="display:block;border-radius:0px;">
                                <li ng-repeat-start="subitem in item.value track by $index" 
                                    ng-if="subitem.value != '---'"
                                    ng-show="isFound(subitem.value + ' ' + subitem.key)">

                                    <a ng-if="!isObject(subitem.value)"
                                       dropdown-toggle href="#" 
                                       ng-click="update(subitem.key);"
                                       value="{{subitem.key}}">
                                        {{ subitem.value}}
                                    </a>
                                </li>
                                <hr ng-repeat-end ng-if="subitem.value == '---'"/>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </li>
                    <hr ng-repeat-end ng-if="item.value == '---'"/>
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
               name="<?= $this->renderName ?>" value='<?= $this->value ?>'/>
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