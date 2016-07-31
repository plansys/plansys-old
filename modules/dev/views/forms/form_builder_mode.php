<style>
    .form-builder-mode .modebar {
        border-bottom: 1px solid rgb(197, 197, 197);
        background: #fff;
        height:27px;
    }
    .form-builder-mode .modebar .mode {
        color:#999;
        border-radius:3px;
        margin:2px 2px 2px 0px;
        float:right;
        font-size:12px;
        cursor:pointer;
        font-weight:bold;
        -webkit-user-select: none;
        user-select: none;
        -moz-user-select: none;
        padding:3px 7px 2px 7px;
    }
    .form-builder-mode .modebar .mode:hover {
        background:#ececeb;
        color:#999;
    }
    .form-builder-mode .modebar .mode.active {
        background:#999;
        color:#fff;
    }
    .form-builder-mode .modebar .selected {
        font-size:11px;
        line-height: 26px;
        margin: 0px 10px;
        color:#999;
    }
    .form-builder-mode .modebar .selected .btn {
        font-size:11px;
        padding:0px 3px;
        color:#666;
        background:white;
    }
</style>
<div class="form-builder-mode">
    <div class="modebar">
        <div class="mode" ng-class="{active:editor.activeTab.mode == 'code'}" 
             ng-click="editor.activeTab.mode = 'code'">Code</div>
        <div class="mode" ng-class="{active:editor.activeTab.mode == 'layout'}" 
             ng-click="editor.activeTab.mode = 'layout'">Layout</div>
        <div class="pull-left selected">
            Selected <i class="fa fa-angle-right"></i> 
            <span ng-if="editor.activeTab.active">
                {{ editor.activeTab.active.name || editor.activeTab.active.name  }}
                <span class="btn btn-xs btn-default" ng-click="editor.formBuilder.builder.unselect()">Clear</span>
            </span>
            <span ng-if="!editor.activeTab.active">Form</span>
        </div>
    </div>
    <div ng-if="editor.activeTab.mode == 'layout'">
        <?php include("form_builder_layout.php"); ?>
    </div>
    <div ng-if="editor.activeTab.mode == 'code'">
        <?php include("form_builder_code.php"); ?>
    </div>
</div>