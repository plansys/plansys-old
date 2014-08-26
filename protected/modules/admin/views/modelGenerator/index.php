<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='17%' min-size="150px" class="sidebar">
            <div ui-header>
                <span ng-show='!saving'>Models</span>
                <span ng-show='saving'>Saving...</span>
            </div>
            <div ui-content>
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list">
                        <li ng-repeat="item in list" ui-tree-node class='menu-list-item'>
                            <a target="iframe"  ng-click="select(this);"  ng-class="is_selected(this)"
                               href="<?php echo $this->createUrl('update', array('class' => '')); ?>{{item.class}}"
                               ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
                                <i class="fa {{item.name == 'MainMenu' ? 'fa-sitemap' : 'fa-book'}} fa-nm"></i>
                                <div ng-if='item.exist == "no"' class="pull-right label label-success" style="margin-top:4px;">
                                    New
                                </div><span>{{item.name}}</span>
                            </a>
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
