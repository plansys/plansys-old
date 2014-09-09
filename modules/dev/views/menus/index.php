

<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='17%' min-size="150px" class="sidebar">
            <div ui-header>
                <!-- Prefered way to add/delete menu is by directly editing the file
                <div class="pull-right">
                    <div ng-show="active != null && active.items == null && active.name != 'MainMenu'"  ng-click="delete()" class="btn btn-xs btn-danger">
                        <i class="fa fa-times"></i>
                        Del
                    </div>
                    <div class="btn btn-xs btn-success" ng-show="active != null" ng-click="new ()">
                        <i class="fa fa-plus"></i>
                        New 
                    </div>
                </div>
                -->
                <span ng-show='!saving'>Menus</span>
                <span ng-show='saving'>Saving...</span>
            </div>
            <div ui-content>
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list">
                        <li ng-repeat="item in list track by $index" ui-tree-node>
                            <div ui-tree-handle ng-click="toggle(this);select(this);"  ng-class="is_selected(this)">

                                <div class="ui-tree-handle-info">
                                    {{item.items.length}} form{{item.items.length > 1 ? 's' : ''}}
                                </div>

                                <span>
                                    <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
                                    <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>
                                </span>
                                {{item.module}}

                            </div>
                            <ol ui-tree-nodes="" ng-model="item.items">
                                <li ng-repeat="subItem in item.items" ui-tree-node class='menu-list-item'>

                                    <!--                                    
                                    <div class="btn btn-xs pull-right item-btn btn-default"
                                        ng-click='rename(this)' ng-show="subItem.name != 'MainMenu'">
                                       <i class="fa fa-edit"></i> Rename
                                   </div>
                                    -->

                                    <a target="iframe" 
                                       href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{subItem.class}}"
                                       ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
                                        <i class="fa {{subItem.name == 'MainMenu' ? 'fa-sitemap' : 'fa-book'}} fa-nm"></i>
                                        <span>{{subItem.name}}</span>
                                    </a>

                                    <div class="rename-menu" style='display:none;border:1px solid #ccc;'>
                                        <i style='margin:3px 6px 0px 3px;position:absolute;'
                                           class="fa {{subItem.name == 'MainMenu' ? 'fa-sitemap' : 'fa-book'}} fa-nm"></i>
                                        <input type="text" ng-model="active.name" 
                                               ng-enter="toggle(this);" ng-blur="save(this, active.name);" spellcheck="false"
                                               style='background:none;border:none;font-size:12px;outline:0px;width:100%; margin-left:22px;'
                                               />
                                    </div>
                                </li>
                            </ol>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div style="padding:0px 0px 0px 5px;overflow:hidden;border:0px;">
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no" seamless="seamless" name="iframe" frameborder="0" style="width:100%;height:100%;overflow:hidden;">

            </iframe>
        </div>
    </div>
</div>
<?php include('index.js.php'); ?>