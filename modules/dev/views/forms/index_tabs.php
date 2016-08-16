<div ng-controller="TabsController">
    <style>
        .tab-set {
            overflow-y:hidden;
        }
        .editor-tab-container {
            border-bottom:0px !important;
            box-shadow:0px 1px 0px 0px #ccc;
            height:27px !important;
        }
        .editor-tab-container > li {
            margin-top:0px;
            width: 124px;
        }
        .editor-tab-container > li > a {
            background:#f9f9f9;
            padding:0px;
        }
        .editor-tab-container > li > a .is-dupe {
            color:#888;
            font-weight: normal;
            font-size:90%;
        }
        .editor-tab-container > li.active > a{
            height:24px;
            background:#fff !important;
            display:block;
            margin-bottom:-1px;
        }   
        .editor-tab-container > li > a i {
            color:#999;
            font-size:11px;
            margin-top:1px;
        }
        .editor-tab-container > li > a > .angular-ui-tree-handle {
            width:90px;
            display:block;
            overflow-x:hidden;
            font-size:11px;
            font-weight:bold;
            line-height: 15px;
            min-height:16px;
            color:#888;
            padding:0px;
            margin:-1px 0px 0px -3px;
            background:transparent;
        }
        .editor-tab-container > li > a .tab-title {
            width:70px;
            overflow-x:hidden;
        }

        .editor-tab-container > li > a i.tab-icon {
            font-size:14px !important;
            float:left;
            margin:1px 5px 0px 0px;
        }

        .editor-tab-container > li > a i {
            cursor:pointer;
        }
        .editor-tab-container > li > a i:hover  {
            color:#000;
        }
        .editor-tab-container > li > a > .icon-btn {
            width:0px;
            display:table;
            text-align:right !important;
            float:right !important;
            position:relative !important;
        }

        .editor-tab-container > li > a  .loading-btn { display:none; font-size:12px; } 
        .editor-tab-container > li > a  .close-btn { display:block; }
        .editor-tab-container > li > a > .icon-btn.loading > .loading-btn { display:block; } 
        .editor-tab-container > li > a > .icon-btn.loading > .close-btn { display:none; }
        .editor-tab-container > li > a > .icon-btn:hover > .loading-btn { display:none; }
        .editor-tab-container > li > a > .icon-btn:hover > .close-btn { display:block; }

        body > .editor-tab-container {
            box-shadow:none;
        }
        body > .editor-tab-container > li > a {
            border:1px solid #ccc !important;
            border-bottom:1px solid #ececeb !important;
            padding:4px 8px 3px 8px;
        }
        body > .editor-tab-container > li > a .icon-btn {
            display:none;
        }
    </style>
    <div ng-if="editor.tabs.length > 0" class="tab-set single-tab" style="margin:0px;" >
        <div class="nav nav-tabs">
            <div oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree="tabTreeOptions" ng-init="treeName = 'tabs'" data-drag-delay="150" data-horizontal="">
                    <ul class="nav nav-tabs editor-tab-container" ui-tree-nodes ng-model="editor.tabs">
                        <li ng-repeat="(idx, tab) in editor.tabs" ui-tree-node 
                            class="editor-tab editor-tab-{{$index}}" alias="{{tab.alias}}"
                            ng-click="editor.selectTab(tab.alias, $event)" ng-init="initTab($index, tab)" >
                            <a>
                                <div class="icon-btn">
                                    <i class="loading-btn fa fa-circle-o-notch fa-spin " ></i>
                                    <i class="close-btn fa fa-times" ng-click="editor.closeTab(tab.alias, $event)"></i>
                                </div>
                                <div ui-tree-handle>
                                    <i class="tab-icon {{tab.icon}}"></i>
                                    <div class="tab-title">
                                        {{ tab.shortName || tab.class }}&nbsp;<span class="is-dupe" ng-if="tab.isDuplicate && !!tab.shortName">{{ tab.class}}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content" ng-if="editor.activeTab">
            <?php include("form_builder.php"); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller("TabsController", function ($scope, $http, $timeout, $localStorage) {
        var storageDelay = 1000;
        var tabLoadDelay = 0;
        $scope.editor = editor;
        editor.tabs = $localStorage.formBuilder.tabs || [];
        editor.activeTab = $localStorage.formBuilder.activeTab || false;
        editor.modelList = $localStorage.modelBuilder.models || {};
        $scope.tabTreeOptions = {
            accept: function (source, dest, opt) {
                if (source.treeName == 'tabs') {
                    return true;
                }
            }
        };

        $scope.initTab = function (idx, tab) {
            if(idx + 1 == editor.tabs.length) {
                $timeout(function () {
                    $(".editor-tab[alias='" + editor.activeTab.alias + "']").addClass("active");
                }, 100);
            }
        }
        editor.load = function (alias, shortName) {
            if (!editor.loadTab(alias)) {
                var tab = {
                    type: 'form',
                    icon: 'fa fa-file-text-o fa-nm',
                    alias: alias,
                    mode: 'layout',
                    class: alias.split(".").pop(),
                    shortName: shortName,
                    fields: [],
                    active: null,
                    modelFieldList: {},
                    relFieldList: {},
                    sidebar: {
                        toolbar: false,
                        properties: true
                    }
                }

                $http.get(Yii.app.createUrl('/dev/forms/getFields', {alias: alias})).success(function (data) {
                    tab.fields = data.fields;
                    tab.modelFieldList = data.modelFieldList;
                    tab.relFieldList = data.relFieldList;
                    tab.form = data.form;
                    tab.formType = data.formType;
                    editor.modelList = data.modelList;
                    if (data.fields.length == 0) {
                        editor.stopLoading(tab.alias);
                    }
                });
                editor.tabs.push(tab);
                editor.loadTab(alias);
                $timeout(function () {
                    $localStorage.formBuilder.tabs = editor.tabs;
                    $localStorage.modelBuilder.models = editor.modelList;
                }, storageDelay);
            }
        }

        editor.detectTabDuplicate = function () {
            var hash = {};
            editor.tabs.forEach(function (tab) {
                if (!hash[tab.shortName]) {
                    tab.isDuplicate = false;
                    hash[tab.shortName] = [tab];
                } else {
                    hash[tab.shortName].push(tab);
                    hash[tab.shortName].forEach(function (item) {
                        item.isDuplicate = true;
                    });
                }
            });
        }

        editor.loadTab = function (alias) {
            editor.detectTabDuplicate();
            if (editor.activeTab) {
                editor.stopLoading(editor.activeTab.alias);
            }
            for (var i in editor.tabs) {
                var tab = editor.tabs[i];
                if (tab.alias === alias) {
                    $(".editor-tab.active").removeClass("active");
                    $(".editor-tab[alias='" + alias + "']").addClass("active");
                    editor.startLoading(tab.alias);
                    $timeout(function () {
                        editor.activeTab = editor.tabs[i];
                        switch (editor.activeTab.type) {
                            case 'form':
                                if (typeof editor.formBuilder.onload == "function") {
                                    editor.formBuilder.onload();
                                }
                                break;
                        }

                        $(".editor-tab.active").removeClass("active");
                        $(".editor-tab[alias='" + alias + "']").addClass("active");
                    }, tabLoadDelay);
                    $timeout(function () {
                        $localStorage.formBuilder.activeTab = editor.tabs[i];
                    }, storageDelay);
                    return true;
                }
            }

            return false;
        }

        editor.startLoading = function (alias) {
            $(".editor-tab[alias='" + alias + "'] .icon-btn").addClass("loading");
        }
        editor.stopLoading = function (alias) {
            $(".editor-tab[alias='" + alias + "'] .icon-btn").removeClass("loading");
        }

        editor.selectTab = function (alias, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (editor.activeTab.alias != alias) {
                location.hash = '#' + alias;
                editor.selectInTree(alias, function () {
                    editor.loadTab(alias);
                });
            }
            $('.form-builder').scrollTop(0);
        }

        editor.closeTab = function (alias, e) {
            e.preventDefault();
            e.stopPropagation();
            var prevTab = null;
            for (var i in editor.tabs) {
                var tab = editor.tabs[i];
                if (tab.alias === alias) {
                    editor.tabs.splice(i, 1);
                    if (tab.alias === editor.activeTab.alias) {
                        if (prevTab !== null) {
                            editor.selectTab(prevTab.alias);
                        } else if (editor.tabs.length > 0) {
                            editor.selectTab(editor.tabs[0].alias);
                        }
                    }
                }
                prevTab = tab;
            }
            editor.detectTabDuplicate();
            $timeout(function () {
                $localStorage.formBuilder.tabs = editor.tabs;
            }, storageDelay);
        }
    });
</script>