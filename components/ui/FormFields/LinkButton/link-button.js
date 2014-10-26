app.directive('linkBtn', function ($timeout, $parse, $compile) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {


            return function ($scope, $el, attrs, ctrl) {

                var generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {


                        var match = url.match(/{([^}]+)}/g);
                        for (i in match) {
                            var m = match[i];
                            m = m.substr(1, m.length - 2);
                            var result = "' + $scope." + m + " + '";
                            if (m.indexOf('.') > 0) {
                                result = $scope.$eval(m);
                            }
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
                    var href = attrs.href;
                    if (href.trim().substr(0, 4) == "url:") {
                        var url = href.trim().substr(4);
                        href = eval(generateUrl(url, 'function'));
                    }

                    $el.attr('href', href);
                    $scope.url = href;
                }

                if (attrs.confirm) {
                    $el.click(function (e) {

                        e.stopPropagation();
                        if (!confirm(attrs.confirm)) {
                            return false;
                        }
                    });
                }

                if (attrs.submit) {
                    var href = attrs.submit;
                    if (href.trim().substr(0, 4) == "url:") {
                        var url = href.trim().substr(4);
                        href = eval(generateUrl(url, 'function'));
                    }

                    $scope.url = href;

                    if (!attrs.ngClick) {
                        $el.attr('ng-click', 'form.submit(this)');
                        $el.replaceWith($compile($el[0])($scope));
                    }
                }
            }
        }
    };
});