<div relation-field <?= $this->expandAttributes($this->options) ?>>

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
        <data name="name" class="hide"><?= $this->name; ?></data>
        <data name="value" class="hide" ><?= $this->value ?></data>
        <data name="include_empty" class="hide" ><?= $this->includeEmpty ?></data>
        <data name="empty_value" class="hide" ><?= $this->emptyValue ?></data>
        <data name="empty_label" class="hide" ><?= $this->emptyLabel ?></data>
        <data name="count" class="hide"><?= $this->count('', []); ?></data>
        <data name="searchable" class="hide" ><?= $this->searchable ?></data>
        <data name="identifier" class="hide" ><?= $this->identifier ?></data>
        <data name="show_other" class="hide" ><?= $this->showOther ?></data>
        <data name="other_label" class="hide" ><?= $this->otherLabel ?></data>
        <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
        <data name="model_field" class="hide"><?= json_encode($model->attributes) ?></data>
        <data name="form_list" class="hide"><?= json_encode($this->list) ?></data>
        <data name="params" class="hide"><?= json_encode($this->params) ?></data>
        <!-- /data -->

        <!-- field -->
        <div class="<?= $this->fieldClass ?>" 
             ng-keydown="dropdownKeypress($event)"
             is-open="isOpen"
             dropdown on-toggle="toggled(open)">

            <!-- default button -->
            <button 
                ng-if="!showOther || (showOther && itemExist())" type="button" 
                <?= $this->expandAttributes($this->fieldOptions) ?> 
                <?php if (@$this->fieldOptions['disabled']): ?>style="opacity:1;background:#fff;border:1px solid #ececeb;"<?php endif; ?> 
                >
                <i ng-show='loading' class="fa fa-spin fa-refresh" 
                   style="position:absolute;right:25px;top:8px;"></i>
                <span <?php if (@$this->fieldOptions['disabled']): ?>style="display:none"<?php endif; ?> 
                                                                     class="caret pull-right"></span>
                <span class="dropdown-text" ng-bind-html="text"></span>
            </button>


            <!-- dropdown item -->
            <div class="dropdown-menu open">
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
                    &mdash; NOT FOUND &mdash;
                </div>
                <ul class="dropdown-menu inner" role="menu">
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
                    <hr ng-if="showOther != ''" />
                    <li class="dropdown-other" ng-if="showOther != ''">
                        <a dropdown-toggle href="#" ng-click="update(otherLabel);" value="{{itemExist() ? otherLabel : value}}">
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