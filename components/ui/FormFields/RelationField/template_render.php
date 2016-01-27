<div relation-field <?= $this->expandAttributes($this->options) ?>>
    <data name="rel_class" rel_class="<?= $this->modelClass ?>" class="hide"></data>

    <!-- label -->
    <?php if ($this->label != ""): ?>
        <label <?= $this->expandAttributes($this->labelOptions) ?>
            class="<?= $this->labelClass ?>" for="<?= $this->label; ?>">
            <?= $this->label ?><?php if ($this->isRequired()) : ?> <div class="required">*</div> <?php endif; ?>
        </label>
    <?php endif; ?>
    <!-- /label -->
    

    <div class="<?= $this->fieldColClass ?>">
        <!-- data -->
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="value" class="hide"><?= $this->value ?></data>
        <data name="include_empty" class="hide"><?= $this->includeEmpty ?></data>
        <data name="empty_value" class="hide"><?= $this->emptyValue ?></data>
        <data name="empty_label" class="hide"><?= $this->emptyLabel ?></data>
        <data name="count" class="hide"><?= $this->count('', []) ?></data>
        <data name="searchable" class="hide"><?= $this->searchable ?></data>
        <data name="identifier" class="hide"><?= $this->identifier ?></data>
        <data name="show_other" class="hide"><?= $this->showOther ?></data>
        <data name="other_label" class="hide"><?= $this->otherLabel ?></data>
        <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
        <data name="model_field" class="hide"><?= htmlentities(json_encode($model->attributes, JSON_PARTIAL_OUTPUT_ON_ERROR)) ?></data>
        <data name="rel_model_class" class="hide"><?= $this->modelClass ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list) ?></data>
        <data name="internal_params" class="hide"><?= json_encode($this->params) ?></data>
        <data name="show_unselect" class="hide"><?= $this->showUnselect; ?></data>
        <data name="id_field" class="hide"><?= $this->idField ?></data>
        <data name="is_disabled" class="hide"><?php
            if (isset($this->fieldOptions['disabled'])) {
                echo $this->fieldOptions['disabled'];
            } else if (isset($this->fieldOptions['ng-disabled'])) {
                echo $this->fieldOptions['ng-disabled'];
                unset($this->fieldOptions['ng-disabled']);
            }
        ?></data>
        <!-- /data -->
        <!-- field -->
        <div class="<?= $this->fieldClass ?>"
             ng-keydown="dropdownKeypress($event)"
             is-open="isOpen"
             dropdown on-toggle="toggled(open)">

            <!-- default button -->
            <div style="{{showUnselect && !!value && !isRelFieldDisabled() ? 'padding-right:30px' : ''}}">
                <div ng-if="showUnselect && !!value && !isRelFieldDisabled() " style="float: right;margin-right:-30px;margin-bottom:-30px;width:33px;border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;" class="btn btn-default btn-sm" ng-click="unselect()">
                    <i class="fa fa-times"></i>
                </div>
                <button
                    ng-if="!showOther || (showOther && itemExist())" type="button"
                    <?= $this->expandAttributes($this->fieldOptions) ?>
                    style="{{ isRelFieldDisabled() ? 'opacity:1;background:#fff;border:1px solid #ececeb;' : ''}}">
                    <i ng-show='loading' class="fa fa-spin fa-refresh"
                       style="position:absolute;right:25px;top:8px;"></i>
                    <span style="{{ isRelFieldDisabled() ? 'display:none' : '' }}" class="caret pull-right"></span>
                    <span class="dropdown-text" ng-bind-html="text"></span>
                </button>
            </div>

            <!-- dropdown item -->
            <div class="dropdown-menu open <?= $this->menuPos; ?>" ng-show="!isRelFieldDisabled()">
                <div class="search" ng-show="searchable" style="margin-bottom:0px;">
                    <input type="text"
                           ng-model="search"
                           ng-delay="500"
                           ng-change="doSearch()"
                           ng-mouseup="searchFocus($event)"
                           placeholder="Search ..."
                           class="input-block-level search-dropdown form-control" autocomplete="off">
                </div>
                <div ng-if="renderedFormList.length == 0"
                     style="text-align:center;padding:15px;font-size:12px;color:#999;">
                    <div ng-if="!loading && jsinternalParamsInitialized">&mdash; NOT FOUND &mdash;</div>
                    <div ng-if="loading || !jsinternalParamsInitialized">&mdash; LOADING &mdash;</div>
                </div>
                <ul ng-if="renderedFormList.length > 0" class="dropdown-menu inner"  style="overflow-x:hidden" role="menu">
                    <li ng-repeat-start="item in renderedFormList track by $index"
                        ng-if="item.value != '---'" class="dropdown-item"
                        ng-class="{'dropdown-header': isObject(item.value)}">

                        <a ng-if="!isObject(item.value)"
                           dropdown-toggle href="#"
                           ng-click="update(item);"
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
                                       ng-click="update(subitem);"
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
                    <hr ng-if="showOther != ''"/>
                    <li class="dropdown-other" ng-if="showOther != ''">
                        <a dropdown-toggle href="#" ng-click="update(otherLabel);"
                           value="{{itemExist() ? otherLabel : value}}">
                            {{ itemExist() ? otherLabel : value}}
                        </a>
                    </li>
                    <hr ng-repeat-end ng-if="item.value == '---'"/>
                    <hr ng-if="count > renderedFormList.length"/>
                    <li ng-if="count > renderedFormList.length">
                        <a href="#" ng-click="next($event)" style="margin-left:-5px;padding-bottom:5px;">
                            <span ng-if="!loading"><i class="fa fa-angle-down"></i> &nbsp; Load More</span>
                            <span ng-if="loading"><i class="fa fa-refresh fa-spin"></i> &nbsp; Loading... </span>
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
        <div ng-if="errors[name]" class="alert error alert-danger">
            {{ errors[name][0] }}
        </div>
        <!-- /error -->
    </div>
</div>