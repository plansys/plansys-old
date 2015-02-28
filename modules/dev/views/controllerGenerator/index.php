
<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='17%' min-size="150px" class="sidebar">
            <div ui-header>
                <span ng-show='!saving'>Controllers</span>
                <span ng-show='saving'>Saving...</span>
            </div>
            <div ui-content oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list">
                        <li ng-repeat="item in list track by $index" collapsed="true" ui-tree-node>
                            <div ui-tree-handle ng-click="toggle(this);
                                            select(this);"  ng-class="is_selected(this)">
                                <div class="ui-tree-handle-info">
                                    {{item.items.length}} controller{{item.items.length > 1 ? 's' : ''}}
                                </div>

                                <span>
                                    <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
                                    <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>
                                </span>
                                {{item.type == 'dev' ? 'Plansys:' : ''}} {{item.module}}
                            </div>
                            <ol ui-tree-nodes="" ng-model="item.items">
                                <li ng-repeat="subItem in item.items" ui-tree-node class='menu-list-item'>
                                    <a target="iframe" 
                                       href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{subItem.class}}"
                                       ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">

                                        <div ng-if='subItem.exist == "no"' 
                                             class="label label-success" 
                                             style="margin-top:4px;position:absolute;right:0px;">
                                            New
                                        </div>
                                        <span style="word-wrap:break-word;padding-left:20px;">
                                            <i style='float:left;margin:3px -20px 0px 0px;'
                                               class="fa {{subItem.name == 'MainMenu' ? 'fa-sitemap' : 'fa-book'}} fa-nm"></i> 
                                            {{formatName(subItem.name)}}
                                        </span>
                                    </a>
                                </li>
                            </ol>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div style="overflow:hidden;border:0px;">
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no" seamless="seamless" name="iframe" frameborder="0" style="width:100%;height:100%;overflow:hidden;">

            </iframe>
        </div>
    </div>
</div>
<?php include('index.js.php'); ?>
