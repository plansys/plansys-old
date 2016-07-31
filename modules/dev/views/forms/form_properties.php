
<div ng-controller="FormBuilderPropertiesController" class="properties-body form-builder-properties">
    <div ng-if="$editor.activeTab.active" class="properties-header" style="margin:-8px -8px 8px -8px">
        <div class='btn btn-danger btn-xs pull-right'
             ng-click='editor.builder.deleteField()'>
            <i class='fa fa-times'></i>
            Delete
        </div>

        <div class="toolbar-type btn-group" dropdown on-toggle="openToolbarType(open)">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle change-type">
                <i style="margin-top:1px;float:left;"
                   class="fa-nm" ng-class="toolbarSettings['icon'][$editor.activeTab.active.type]"></i>
                &nbsp; {{$editor.activeTab.active.type}}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" style="min-width:160px;max-height:200px;" role="menu">
                <li ng-repeat="(name, icon) in toolbarSettings['icon']">
                    <a href="#" dropdown-toggle value="{{name}}"
                       ng-click="$editor.activeTab.active.type = name;
                               save();">
                        <i style="width:20px;margin-left:-5px;"
                           class="{{icon}}"></i> {{name}}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <form class="form-horizontal" role="form"
          ng-if="$editor.activeTab.active == null
                          && $editor.activeTab.layout == null
                          && $editor.activeTab.formType != 'FormField'">
        <div class="properties-header" style="margin:-3px -3px 0px -3px">
            <div ng-if="!propertiesLoading">
                <i class="fa fa-file-text"></i>&nbsp;
                Form Properties 
            </div>
            <div ng-else>
                <i class="fa fa-spin fa-refresh"></i> Loading Field... 
            </div>
        </div>
        <div ui-content style="padding:6px 5px 0px 10px;" ng-if="!$editor.activeTab.active">
            <div ng-controller="FormBuilderPropTabController" ng-init="propertiesLoaded()">
                <?php
                $fp = FormRenderer::load('DevFormProperties');
                echo $fp->render($fp->form);
                ?>
            </div>
        </div>
    </form>

    <div ui-content style="margin-top:-2px"
         ng-if="$editor.activeTab.active != null">

        <form id="toolbar-properties" 
              class="form-horizontal" role="form" onload="propertiesLoaded()"
              ng-include="Yii.app.createUrl('dev/forms/renderProperties', {class: $editor.activeTab.active.type})">
        </form>
    </div>

</div>

<script type="text/javascript">
    app.controller("FormBuilderPropTabController", function ($scope) {

    });
    app.controller("FormBuilderPropertiesController", function ($scope, $http, $timeout, $localStorage) {
        editor.formBuilder.properties = $scope;
        $scope.editor = editor.formBuilder;
        $scope.$editor = editor;
        $scope.propertiesLoading = true;
        
        console.log(editor.activeTab);

        $scope.$watch('$editor.activeTab.form', function (n) {
            $scope.form = n;
        });
        $scope.$watch('$editor.activeTab.layout', function (n) {
            $scope.layout = n;
        });
        $scope.$watch('$editor.activeTab.alias', function (n) {
            $scope.classPath = n;
        });
        $scope.$watch('$editor.activeTab.modelFieldList', function (n) {
            $scope.modelFieldList = n;
        });
        $scope.$watch('$editor.activeTab.relFieldList', function (n) {
            $scope.relFieldList = n;
        });
        $scope.setActive = function (item) {
            $scope.active = item;
            $scope.propertiesLoading = true;
        }
        $scope.propertiesLoaded = function () {
            $timeout(function () {
                $scope.propertiesLoading = false;
            }, 400);
        }
        $scope.toolbarSettings = <?php echo json_encode(FormField::settings()); ?>;
        /*********************** TEXT ********************************/
        $(window).resize(function () {
            $(".text-editor").height($(".form-builder-properties [ui-content]").height() - 50);
        });
        $scope.aceLoaded = function () {
            $(window).resize();
        };
        /************************ DATA SOURCE ****************************/
        $scope.dataSourceList = {};
        $scope.getDataSourceList = function () {
            var dslist = {
                '': '-- EMPTY --',
                '---': '---'
            };

            function recurseFields(f) {
                for (i in f) {
                    var x = f[i];
                    if (typeof f[i] != 'object')
                        continue;

                    if (f[i].type == 'DataSource') {
                        dslist[f[i].name] = f[i].name;
                    }

                    for (k in f[i].parseField) {
                        recurseFields(x[k]);
                    }
                }
            }
            recurseFields(editor.activeTab.fields);
            $scope.dataSourceList = dslist;
        }

        $scope.selectDataField = function (all, idx) {
            var isHidden = !all[idx].$showDF;
            for (var i in all) {
                all[i].$showDF = false;
            }
            if (isHidden) {
                all[idx].$showDF = true;
            }
            if (!!all[idx].$showDFR) {
                delete all[idx].$showDFR;
            }
        }

        /************************ RELATION FIELD  ****************************/
        $scope.relationFieldList = {};
        $scope.generateRelCheckbox = function () {
            $scope.model = $scope.active = editor.activeTab.active;
            $http.get(Yii.app.createUrl('/formfield/RelationField.listFieldByRel', {
                class: $scope.classPath,
                rel: $scope.active.name
            })).success(function (data) {
                $scope.relationFieldList = data;
            });
        }
        $scope.generateRelationField = function (modelClass, parentScope) {
            $scope.model = $scope.active = editor.activeTab.active;
            modelClass = modelClass || $scope.active.modelClass;
            $http.get(Yii.app.createUrl('/formfield/RelationField.listField', {
                class: modelClass
            })).success(function (data) {
                $scope.relationFieldList = data;
                if (parentScope != null && typeof parentScope.updateListView != "undefined") {
                    parentScope.updateListView();
                }
            });
        }
        /************************ TEXT AUTO COMPLETE  ****************************/
        $scope.generateAutoComplete = function () {
            switch ($scope.active.autocomplete) {
                case "rel":
                    $timeout(function () {
                        $scope.active.modelClass = $scope.classPath;
                        $scope.active.idField = $scope.active.name;
                        $scope.active.labelField = $scope.active.name;
                        $scope.active.criteria = {
                            'select': $scope.active.name,
                            'distinct': 'true',
                            'alias': 't',
                            'condition': '{[search]}',
                            'order': '',
                            'group': '',
                            'having': '',
                            'join': ''
                        };
                        $scope.save();
                    });
                    break;
            }
        }

        /************************ DATA CHART SERIES ****************************/
        $scope.generateSeries = function (retrieveMode) {
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
            if (confirm("Your current series will be lost. Are you sure?")) {
                $scope.active.series = [];
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: $scope.active.datasource,
                    class: $scope.classPath,
                    generate: 1
                }).success(function (data) {
                    if (typeof data == 'object') {
                        if (typeof data.data == 'object') {
                            data = data.data;
                        } else {
                            return;
                        }

                        var generated;
                        switch (retrieveMode) {
                            case 'by Row' :
                                generated = generateByRow(data);
                                break;
                            case 'by Column' :
                                generated = generateByColumn(data);
                                break;
                        }
                        if (typeof generated == "object" && generated.length > 0) {
                            $scope.active.series = generated[0];
                            $scope.setTickSeries();
                        } else {
                            alert("Field generation failed");
                        }
                        /*****  FUNCTION *****/

                        function generateByRow(data) {
                            var filtered = [];
                            for (var i in data) {
                                var rowcontent = {};
                                for (var j in data[i]) {
                                    rowcontent[j] = data[i][j];
                                }
                                filtered.push(rowcontent);
                            }

                            var result = [];
                            for (var i in filtered) {
                                if (typeof result[i] == "undefined") {
                                    result[i] = [];
                                }

                                for (var j in filtered[i]) {
                                    var series = angular.extend({}, templateAttr);
                                    series.value = filtered[i][j];
                                    series.label = j;
                                    series.color = getRandomColor();
                                    result[i].push(series);
                                }
                            }

                            return result;
                        }

                        function generateByColumn(data) {
                            var filtered = {};
                            for (var i in data) {
                                for (var j in data[i]) {
                                    if (typeof filtered[j] == "undefined") {
                                        filtered[j] = [];
                                    }
                                    filtered[j].push(data[i][j]);
                                }
                            }

                            var color;
                            var result = [];
                            result[0] = [];
                            for (var i in filtered) {
                                var series = angular.extend({}, templateAttr);
                                series.label = i;
                                series.value = filtered[i];
                                series.color = getRandomColor();
                                result[0].push(series);
                            }

                            return result;
                        }

                        function getRandomColor() {
                            var letters = '0123456789ABCDEF'.split('');
                            var color = '#';
                            for (var i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }

                        $scope.save();
                    }

                });
            }
        }

        $scope.setTickSeries = function () {
            var series = $scope.active.series;
            $scope.tickSeriesList = {
                '': '-- NONE --',
                '---': '---'
            };
            for (var i in series) {
                $scope.tickSeriesList[series[i].label] = series[i].label;
            }
        }

        function toTitleCase(str) {
            // Replace special characters with a space
            str = str.replace(/[^a-zA-Z0-9 ]/g, " ");
            // put a space before an uppercase letter
            str = str.replace(/([a-z](?=[A-Z]))/g, '$1 ');
            // Lower case first character and some other stuff that I don't understand
            str = str.replace(/([^a-zA-Z0-9 ])|^[0-9]+/g, '').trim().toLowerCase();
            // uppercase characters preceded by a space or number
            str = str.replace(/([ 0-9]+)([a-zA-Z])/g, function (a, b, c) {
                return b.trim() + ' ' + c.toUpperCase();
            });
            return str[0].toUpperCase() + str.substr(1);
        }

        /************************ DATA FILTERS ****************************/
        $scope.generateFilters = function () {
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
            if (confirm("Your current filters will be lost. Are you sure?")) {
                $scope.active.filters = [];
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: $scope.active.datasource,
                    class: $scope.classPath,
                    generate: 1
                }).success(function (data) {
                    if (typeof data == 'object') {
                        if (typeof data.data == 'object') {
                            data = data.data;
                        } else {
                            return;
                        }

                        if (data != null && data.length > 0 && typeof data[0] == "object") {
                            for (i in data[0]) {
                                var filter = angular.extend({}, templateAttr);
                                filter.name = i;
                                filter.label = toTitleCase(i);
                                if (i == 'id') {
                                    filter.filterType = 'number';
                                }

                                if (typeof data[0][i] == "string" && data[0][i].match(/\d\d\d\d-(\d)?\d-(\d)?\d(.*)/g)) {
                                    filter.filterType = 'date';
                                }

                                $scope.active.filters.push(filter);
                            }
                            $scope.save();
                        } else {
                            alert("WARNING: Filter Generator failed!\n\nYour query result is empty.\nPlease make sure your result returned more than one row.\n\n");
                        }
                    }

                });
            }
        }

        /************************ DATA COLUMNS ****************************/
        $scope.dsGroupCols = {};
        $scope.getDSGroupCols = function () {
            var name = $scope.active.name;
            if (!!$scope.active.datasource) {
                name = $scope.active.datasource;
            }
            if (!!name) {
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: name,
                    class: $scope.classPath,
                    generate: 1
                }).success(function (data) {
                    if (typeof data == 'object') {
                        if (typeof data.data == 'object') {
                            data = data.data;
                        } else {
                            return;
                        }

                        $scope.dsGroupCols = {};
                        if (!!$scope.active.datasource) {
                            $scope.dsGroupCols = {'': 'Current Column', '---': '---'};
                        }
                        if (data != null && data.length > 0 && typeof data[0] == "object") {
                            for (i in data[0]) {
                                $scope.dsGroupCols[i] = i;
                            }
                        }
                    }
                });
            }
        }
        $scope.generateColumns = function () {
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
            if (confirm("Your current columns will be lost. Are you sure?")) {
                $scope.active.columns.splice(0, $scope.active.columns.length);
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: $scope.active.datasource,
                    class: $scope.classPath,
                    generate: 1
                }).success(function (data) {
                    if (typeof data == 'object') {
                        if (typeof data.data == 'object') {
                            data = data.data;
                        } else {
                            return;
                        }

                        if (data != null && data.length > 0 && typeof data[0] == "object") {
                            for (i in data[0]) {
                                var filter = angular.extend({}, templateAttr);
                                for (k in filter) {
                                    if (['columnType', 'name', 'label', 'show'].indexOf(k) < 0
                                            && templateAttr.typeOptions[filter.columnType].indexOf(k) < 0) {
                                        delete filter[k];
                                    }
                                }

                                filter.name = i;
                                filter.label = toTitleCase(i);
                                $scope.active.columns.push(filter);
                            }
                            $scope.save();
                        } else {
                            alert("WARNING: Column Generator failed!\n\nYour query result is empty.\nPlease make sure your query has returned any row!\n\n");
                        }
                    }
                });
            }
        }
    });
</script>