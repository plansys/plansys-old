<div ng-controller="<?=$class?>MenuTree">
    <div ui-header>
        <?php if ($icon != ""): ?>
            <i style="float:left;margin:8px 3px 0px -10px;" class="fa fa-fw <?=$icon?>"></i>
        <?php endif;?>
        <?php echo $title;?>
    </div>

    <div ui-content>
        <div oc-lazy-load="{name: 'ng-context-menu', files: [
             '<?=Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true);?>'
             ]}">
            <div oc-lazy-load="{name: 'ui.tree', files: ['<?=Yii::app()->controller->staticUrl('/js/lib/angular.ui.tree.js')?>']}">
                <script type="text/ng-template"
                id="<?=$class?>MenuTreeLoop"><?php include 'menu_layout.php';?></script>
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes ng-model="list" >
                        <li data-collapsed="item.state == 'collapsed'"
                            ng-repeat="item in list"
                            ui-tree-node ng-include="'<?=$class?>MenuTreeLoop'"></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($script != false): ?>
    <script type="text/javascript">
    <?php echo $script?>
        registerController("<?=$class?>MenuTree");
    </script>
<?php endif;?>