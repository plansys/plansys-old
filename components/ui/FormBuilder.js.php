
<?php
if (count(@$renderParams['errors']) > 0) {
    if (isset($data['errors'])) {
        $data['errors'] = array_merge($data['errors'], $renderParams['errors']);
    } else {
        $data['errors'] = $renderParams['errors'];
    }
}
if (Yii::app()->user->hasFlash('error')) {
    $errors = Yii::app()->user->getFlash('error');
    if (!is_array($errors)) {
        $errors = [$errors];
    }
    $data['errors'] = ['id' => $errors];
}
ob_start();
?>
<script type = "text/javascript">
    <?php ob_start(); ?>
    var plansys = <?php echo json_encode(Setting::get('app')); ?>;
    
    app.controller("<?php echo $modelClass ?>Controller", function ($scope, $parse, $timeout, $http, $localStorage, $filter) {
        $scope.form = <?php echo json_encode($this->form); ?>;
        $scope.model = <?php echo @json_encode($data['data'], JSON_PARTIAL_OUTPUT_ON_ERROR); ?>;
        $scope.errors = <?php echo @json_encode($data['errors']); ?>;
        $scope.renderParams = <?php echo @json_encode($renderParams); ?>;
        $scope.getParams = <?php echo @json_encode($_GET); ?>;
        $scope.params = angular.extend({}, $scope.renderParams, $scope.getParams);
        $scope.pageUrl = "<?php echo @Yii::app()->request->url; ?>";
        $scope.pageInfo = <?php echo json_encode(AuditTrail::getPathInfo()) ?>;
        $scope.formClass = "<?php echo $modelClass; ?>";
        $scope.formClassPath = "<?php echo $modelClassPath; ?>";
        $scope.modelBaseClass = "<?php echo ActiveRecord::baseClass($this->model); ?>";
        $scope.lastModified = "<?php echo Helper::getLastModified($modelClass); ?>";
        $scope.date = date;
        $scope.strtotime = strtotime;
        $scope.angular = angular;
        $scope._fields = {};
        window.csrf = {
            name: "<?php echo Yii::app()->request->csrfTokenName; ?>",
            token: "<?php echo Yii::app()->request->csrfToken; ?>"
        };
        
        // init scope on current element
        window.appScope = $scope;
        
        if ($scope.model == null || !$scope.model) {
            $scope.model = {};
        }
        
        if (window.plansys) {
            window.plansys.rootPath = '<?php echo str_replace('\\','/',Setting::getRootPath()); ?>';
            window.plansys.repoPath = '<?php echo str_replace('\\','/',Setting::getRootPath() . DIRECTORY_SEPARATOR . Setting::get('repo.path')); ?>';
            window.plansys.baseUrl = "<?php echo Yii::app()->baseUrl ?>";
        }

        // Process Relation Error Message
        for (i in $scope.errors) {
            if ($scope.errors[i].length > 0 && angular.isObject($scope.errors[i][0])) {
                var err = [];
                var error = $scope.errors[i][0];
                if (!error.type || !error.list) continue;
                
                switch (error.type) {
                    case "CHasManyRelation":
                    case "ManyManyRelation":
                        for (li in error.list) {
                            for (er in error.list[li].errors) {
                                err.push(error.list[li].errors[er]);
                            }
                        }
                        break;
                }
                
                $scope.errors['relationErrorText'] = ['Terdapat kesalahan entri, mohon periksa kembali kelengkapan data anda:<br/><div style="font-size:80%;padding-left:10px;">' + err.join("<br/>") + '</div>'];
            }
        }

        // PopUp Window Helper
        $scope.closeWindow = function() {
            window.close();
        }
        
        // Define parentWindow for popup
        try { 
            if (window.opener && window.opener.formScope) {
                $scope.parentWindow = window.opener.formScope;
            }
        } catch (e) {}
        
        if ($scope.pageInfo.ctrl == 'formfield' && $scope.pageInfo.action == 'subform') {
            if (!!$scope.params.f && !!window.opener.popupScope) {
                $scope.popupScope = window.opener.popupScope[$scope.params.f];
            }
        }

        // initialize pageSetting
        $timeout(function () {
            var $storage = $localStorage;
            $storage.pageSetting = $storage.pageSetting || {};
            $storage.pageSetting[$scope.pageInfo.pathinfo] = $storage.pageSetting[$scope.pageInfo.pathinfo] || {};
            if ($storage.pageSetting[$scope.pageInfo.pathinfo].lastModified !== $scope.lastModified) {
                $storage.pageSetting[$scope.pageInfo.pathinfo] = {};
            }
            $scope.pageSetting = $storage.pageSetting[$scope.pageInfo.pathinfo];
            $scope.pageSetting.lastModified = $scope.lastModified;

            // reset page setting when...
            if ($scope.form.layout == "dashboard" || $scope.pageInfo['module'] == 'sys') {
                $scope.resetPageSetting();
            }
        });

        $scope.resetPageSetting = function () {
            var $storage = $localStorage;
            $storage.pageSetting = $storage.pageSetting || {};
            $storage.pageSetting[$scope.pageInfo.pathinfo] = {};
            $scope.pageSetting = {};
        }

        // audit trail tracker
        $timeout(function () {
            // send current page title with id to tracker
            $scope.pageInfo['description'] = $scope.form.title
            $scope.pageInfo['form_class'] = $scope.formClass;
            $scope.pageInfo['model_class'] = $scope.modelBaseClass;
            if ($("[ps-action-bar] .action-bar .title").text() != '') {
                $scope.pageInfo['description'] = $("[ps-action-bar] .action-bar .title").text();

                if (!!$scope.model && !!$scope.model.id) {
                    $scope.pageInfo['description'] += " [#" + $scope.model.id + "]";
                }
            }

            if ($scope.user != null) {
                // track create or update in audit trail
                $scope.pageInfo[window.csrf.name] = window.csrf.token;
                $http.post(Yii.app.createUrl('/sys/auditTrail/track'), $scope.pageInfo);
            }
        }, 1000);

        $scope.rel = {};

        <?php if (!Yii::app()->user->isGuest) : ?>
        $scope.user = <?php echo @json_encode(Yii::app()->user->info); ?>;
        if ($scope.user != null) {
            $scope.user.role = [];
            for (i in $scope.user.roles) {
                $scope.user.role.push($scope.user.roles[i]['role_name']);
            }

            $scope.user.isRole = function (role) {
                for (i in $scope.user.role) {
                    var r = $scope.user.role[i];
                    if (r.indexOf(role + ".") == 0) {
                        return true;
                    }
                }

                for (i in $scope.user.role) {
                    var r = $scope.user.role[i];
                    if (r.indexOf(role + ".") == 0) {
                        return true;
                    }
                }

                return $scope.user.role.indexOf(role) >= 0;
            }
        }
        <?php endif; ?>

        <?php if (is_object(Yii::app()->controller) && is_object(Yii::app()->controller->module)) : ?>
        $scope.module = '<?php echo Yii::app()->controller->module->id ?>';
        <?php endif; ?>

        <?php if (Yii::app()->user->hasFlash('info')) : ?>
        $scope.flash = '<?php echo Yii::app()->user->getFlash('info'); ?>';
        <?php endif; ?>

        <?php if (isset($data['validators'])) : ?>
        $scope.validators = <?php echo @json_encode($data['validators']); ?>;
        <?php endif; ?>

        <?php if (is_subclass_of($this->model, 'ActiveRecord') && isset($data['isNewRecord'])) : ?>
        $scope.isNewRecord = <?php echo $data['isNewRecord'] ? 'true' : 'false' ?>;
        <?php endif; ?>

        $scope.form.title = document.title;
        $scope.$watch('form.title', function () {
            document.title = $scope.form.title;
        });

        $scope.formSubmitting = false;
        $scope.startLoading = function () {
            $scope.formSubmitting = true;
        }
        $scope.submitted = false;

        $scope.form.submit = function (button) {
            function submit() {
                $timeout(function() {
                    if (typeof button == "object") {
                        $scope.formSubmitting = true;
                        $scope.submitted = true;
                        if (!!button) {
                            var baseurl = button.url;
                            if (typeof button.url != 'string' || button.url.trim() == '' || button.url.trim() == '#') {
                                baseurl = '<?php echo Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                            }
    
                            var parseParams = $parse(button.urlparams);
                            var urlParams = angular.extend($scope.getParams, parseParams($scope));
                        } else {
                            var baseurl = '<?php echo Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                            var urlparams = {};
                        }
                        var url = Yii.app.createUrl(baseurl, urlParams);
                        $("div[ng-controller=<?php echo $modelClass ?>Controller] form").attr('action', url).submit();
                    }
                });
            }

            if (!$scope.submitted) {
                if (!!$scope.model && $scope.user != null) {
                    // track create or update in audit trail
                    $scope.formSubmitting = true;
                    if (!!$scope.model) {
                        $scope.pageInfo['data'] = JSON.stringify($scope.model);
                        $scope.pageInfo['form_class'] = $scope.formClass;
                        $scope.pageInfo['model_class'] = $scope.modelBaseClass;
                        $scope.pageInfo['model_id'] = $scope.model.id;
                        
                        if ($scope.pageInfo['data'].length > 5000) {
                            $scope.pageInfo['data'] = "";
                        }
                    }

                    var type = $scope.isNewRecord ? 'create' : 'update';
                    if ($scope.pageInfo['action'] == 'actionIndex') {
                        type = 'update';
                    }
                    
                    <?php if (Setting::get('app.auditTrail') != 'Disabled') : ?>
                    // send tracking information
                    $http.post(Yii.app.createUrl('/sys/auditTrail/track', {
                        t: type
                    }), $scope.pageInfo).success(function (d) {
                        submit();
                    });
                    <?php else : ?>
                    submit();
                    <?php endif; ?>
                } else {
                    submit();
                }
            }
        };

        $("div[ng-controller=<?php echo $modelClass ?>Controller] form").submit(function (e) {
            if ($scope.uploading.length > 0) {
                alert("Mohon tunggu sampai proses file upload selesai.");
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            $scope.form.submit();
        });

        $scope.form.canGoBack = function () {
            return (document.referrer == "" || window.history.length > 1);
        }

        $scope.form.goBack = function () {
            window.history.back();
        }

        $scope.uploading = [];
        $scope.dataGrids = {length: 0};
        $("[ng-controller=<?php echo $modelClass ?>Controller] .data-grid-loader").each(function () {
            $scope.dataGrids[$(this).attr('name')] = false;
            $scope.dataGrids.length++;
        });
        function inlineJS() {
            $("div[ng-controller=<?php echo $modelClass ?>Controller]").css('opacity', 1);
            <?php echo implode("\n            ", explode("\n", $inlineJS)); ?>
        }

        function inlineJS2() {
            $("div[ng-controller=<?php echo $modelClass ?>Controller]").css('opacity', 1);
            <?php echo implode("\n            ", explode("\n", @$inlineJS2)); ?>
        }
        
        // Service related JS
        $scope.service = {
            services: {},
            serverTime: null,
            timeDiff: 0,
            start: function(serviceName, params) {
                if (typeof params !== 'object') {
                    params = {
                        name: serviceName
                    }
                }
                $http.get(Yii.app.createUrl('/sys/serviceApi/start', params)).success(function() {
                    this.watch(serviceName);
                }.bind(this));
            },
            runtime: function(serviceName) {
                if (!this.services[serviceName]) {
                    this.watch(serviceName);
                }
                else {
                    return this.services[serviceName].runtime;
                }
                return '';
            },
            running: function(serviceName) {
                if (!this.services[serviceName]) {
                    this.watch(serviceName);
                }
                else {
                    return this.services[serviceName].running;
                }
                return '';
            },
            stop: function(serviceName) {
                if (typeof params !== 'object') {
                    params = {
                        name: serviceName
                    }
                }
                $http.get(Yii.app.createUrl('/sys/serviceApi/stop', params)).success(function() {
                    this.watch(serviceName);
                }.bind(this));
            },
            watch: function(serviceName) {
                if (!this.serverTime) {
                    if (this.serverTime !== false) {
                        $http.get(Yii.app.createUrl('/sys/serviceApi/time')).success(function(res) {
                            this.serverTime = res;
                            this.timeDiff = strtotime(date("Y-m-d H:i:s")) - strtotime(this.serverTime)  +1 ;
                            this.watch(serviceName);
                        }.bind(this));
                        
                        this.serverTime = false;
                        return;
                    }
                }
                
                svc = this.services;
                
                //only run when not watched
                if (!svc[serviceName] || !svc[serviceName].watched) {
                    if (!svc[serviceName]) {
                        svc[serviceName] = { 
                            running: false,
                            lastrun: 'Never',
                            runtime: '0',
                            timer: false,
                            watched: true
                        }
                    }
                    
                    s = svc[serviceName];
                    function restartTimer(that) {
                        if (s.timer) {
                            $timeout.cancel(s.timer);
                        }
                        
                        s.timer = $timeout(function() {
                            s.watched = false;
                    
                            that.watch(serviceName);
                        }.bind(that), s.running ? 300 : 1000);
                    };
                    
                    $http.get(Yii.app.createUrl('/sys/serviceApi/info', {name:serviceName}))
                    .then(
                    function(res) {
                        $timeout(function() {
                            if (res.data.instances > 0) {
                                s.running = true;
                                s.lastrun = res.data.lastrun;
                                
                                var ctime = strtotime(date("Y-m-d H:i:s")); //client time
                                var stime = strtotime(s.lastrun); // server time
                                
                                if (ctime < stime) {
                                    ctime = stime;
                                }
                                s.runtime = ctime - stime + this.timeDiff;
                            } else {
                                s.running = false;
                                s.runtime = '0';
                            }
                            restartTimer(this);
                        }.bind(this));
                    }.bind(this),
                    function(res) { 
                        restartTimer(this); 
                    }.bind(this));
                
                }
                
            }
        };
        
        // execute inline JS
        $timeout(function () {
            // make sure datagrids is loaded before executing inlinejs
            if ($scope.dataGrids.length > 0) {
                var dgWatch = $scope.$watch('dataGrids.length', function (n) {
                    if (n == 0) {
                        dgWatch();
                        inlineJS();
                    }
                }, true);
            } else {
                inlineJS();
                inlineJS2();
            }
        });
    });
<?php $script = ob_get_clean(); ?>
</script >
<?php
ob_get_clean();

return $script;
