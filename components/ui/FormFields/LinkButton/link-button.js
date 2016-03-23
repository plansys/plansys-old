app.directive('linkBtn', function ($timeout, $parse, $compile, $http) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            var ngClick = null;
            if (attrs.ngClick) {
                ngClick = attrs.ngClick;
                delete attrs.ngClick;
            }
            return function ($scope, $el, attrs, ctrl) {
                var urlVars = [];
                var generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {
                        var match = url.match(/{([^}]+)}/g);
                        urlVars.length = 0;
                        for (i in match) {
                            var m = match[i];
                            m = m.substr(1, m.length - 2);
                            var result = "' + $scope." + m + " + '";
                            if (m.indexOf('.') > 0) {
                                result = $scope.$eval(m);
                            }
                            urlVars.push(m);
                            url = url.replace('{' + m + '}', result);
                        }

                        if (url.match(/http*/ig)) {
                            output = url.replace(/\{/g, "'+ $scope.").replace(/\}/g, " + '");
                        } else if (url.trim() == '#') {
                            output = '#';
                        } else {
                            url = url.replace(/\?/ig, '&');
                            output = "Yii.app.createUrl('" + url + "')";
                        }

                        if (type == 'html') {
                            if (output != '#') {
                                output = '{{' + output + '}}';
                            }
                        }
                    }
                    return output;
                }

                if ($el.attr('group') != '' && $(".link-btn[group=" + $el.attr('group') + "]").length > 1) {
                    $firstBtn = $(".link-btn[group=" + $el.attr('group') + "]").eq(0);
                    if (!$firstBtn.parent().hasClass('btn-group')) {
                        $firstBtn.wrap("<div class='btn-group'></div>");
                    }

                    $el.css('opacity', '1').appendTo($firstBtn.parent());
                } else {
                    $el.css('opacity', '1');
                }


                if (attrs.href) {
                    $timeout(function () {
                        var href = attrs.href;
                        var originalHref = href;

                        if (href.trim().substr(0, 4) == "url:") {
                            var url = href.trim().substr(4);
                            href = eval(generateUrl(url, 'function'));

                            //watch URL Variables
                            for (i in urlVars) {
                                $scope.$watch(urlVars[i], function (n) {
                                    var url = originalHref.trim().substr(4);
                                    href = eval(generateUrl(url, 'function'));
                                    $el.attr('href', href);
                                    $scope.url = href;
                                });
                            }
                        }

                        $el.attr('href', href);
                        $scope.url = href;
                    });
                }

                $scope.trackDelete = function (e) {
                    e.stopPropagation();
                    e.preventDefault();

                    $scope.startLoading();

                    function continueDefault() {
                        if (!!attrs.submit) {
                            submit();
                        } else if (!!attrs.href) {
                            location.href = $scope.url;
                        }
                    }

                    if (!!$scope.pageInfo) {
                        if (!!$scope.model) {
                            $scope.pageInfo['data'] = JSON.stringify($scope.model);
                            $scope.pageInfo['form_class'] = $scope.formClass;
                            $scope.pageInfo['model_class'] = $scope.modelBaseClass;
                            $scope.pageInfo['model_id'] = $scope.model.id;
                        }
                        $http.post(Yii.app.createUrl('/sys/auditTrail/track', {
                            t: 'delete'
                        }), $scope.pageInfo).success(function () {
                            continueDefault();
                        });
                    } else {
                        continueDefault();
                    }
                }

                $scope.isDeleteButton = function (e) {

                    if ($el.hasClass('btn-danger')) {
                        if (/hapus|delete|del/ig.test($el.text())) {
                            return true;
                        }
                    }

                    return false;
                }


                $el.click(function (e) {
                    if (attrs.confirm) {
                        e.stopPropagation();
                        if (!confirm(attrs.confirm)) {
                            e.preventDefault();
                            return false;
                        }

                        if ($scope.isDeleteButton(e)) {
                            $scope.trackDelete(e);
                            return false;
                        }
                    } else if (attrs.prompt) {
                        e.stopPropagation();
                        if (prompt(attrs.prompt) != attrs.promptIf) {
                            return false;
                        }

                        if ($scope.isDeleteButton(e)) {
                            $scope.trackDelete(e);
                            return false;
                        }
                    } else {
                        if ($scope.isDeleteButton(e)) {
                            $scope.trackDelete(e);
                            return false;
                        }
                    }
                    $timeout(function () {
                        $scope.$eval(ngClick);
                    });
                });


                if (!!attrs.submit) {
                    var href = attrs.submit;
                    if (href.trim().substr(0, 4) == "url:") {
                        var url = href.trim().substr(4);
                        href = $scope.$eval(generateUrl(url, 'function'));
                    }
                    $scope.url = href;

                    if (!attrs.ngClick) {
                        ngClick = 'form.submit(this)';
                    }
                }
            }
        }
    };
});