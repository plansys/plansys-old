<div ng-controller="<?= $class ?>MenuTree">
    <div ui-header>
        <?php echo $title; ?>
    </div>
    <div ui-content>
        <script type="text/ng-template" id="<?= $class ?>MenuTreeLoop"><?php include('menu_layout.php'); ?></script>
        <div ui-tree data-drag-enabled="false">
            <ol ui-tree-nodes ng-model="list" >
                <li data-collapsed="isCollapsed(item)" ng-repeat="item in list" ui-tree-node ng-include="'<?= $class ?>MenuTreeLoop'"></li>
            </ol>
        </div>
    </div>
</div>
<?php if ($script != false): ?>
    <script type="text/javascript">
    <?php echo $script ?>
        registerController("<?= $class ?>MenuTree");
    </script>
<?php endif; ?>