if (!Array.prototype.filter) {
    Array.prototype.filter = function (fn, context) {
        var i,
                value,
                result = [],
                length;
        if (!this || typeof fn !== 'function' || (fn instanceof RegExp)) {
            throw new TypeError();
        }
        length = this.length;
        for (i = 0; i < length; i++) {
            if (this.hasOwnProperty(i)) {
                value = this[i];
                if (fn.call(context, value, i, this)) {
                    result.push(value);
                }
            }
        }
        return result;
    };
}

if (!Object.getProperty) {
    Object.getProperty = function (obj, path, def) {

        for (var i = 0, path = path.split('.'), len = path.length; i < len; i++) {
            if (!obj || typeof obj !== 'object')
                return def;
            obj = obj[path[i]];
        }

        if (obj === undefined)
            return def;
        return obj;
    }
}


function registerController(controllerName) {
    if (controllerProvider == null)
        return;
    // Here I cannot get the controller function directly so I
    // need to loop through the module's _invokeQueue to get it
    var queue = angular.module("main")._invokeQueue;
    for (var i = 0; i < queue.length; i++) {
        var call = queue[i];
        if (call[0] == "$controllerProvider" &&
                call[1] == "register" &&
                call[2][0] == controllerName) {
            controllerProvider.register(controllerName, call[2][1]);
        }
    }
}

var controllerProvider = null;
var app = angular.module("main", [
    'ui.layout',
    'ui.tree',
    'ui.bootstrap',
    'ngGrid',
    'angularFileUpload',
    'ngStorage',
]);
app.config(function ($sceProvider, $controllerProvider) {
    controllerProvider = $controllerProvider;
    $sceProvider.enabled(false);
});
app.filter('capitalize', function () {
    return function (input, scope) {
        if (input != null)
            input = input.toLowerCase();
        return input.substring(0, 1).toUpperCase() + input.substring(1);
    }
});
app.filter('fileSize', function () {
    return function (size, precision) {

        if (precision == 0 || precision == null) {
            precision = 1;
        }
        if (size == 0 || size == null) {
            return "";
        }
        else if (!isNaN(size)) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            var posttxt = 0;

            if (size < 1024) {
                return Number(size) + " " + sizes[posttxt];
            }
            while (size >= 1024) {
                posttxt++;
                size = size / 1024;
            }

            var power = Math.pow(10, precision);
            var poweredVal = Math.ceil(size * power);

            size = poweredVal / power;

            return size + " " + sizes[posttxt];
        } else {
            console.log('Error: Not a number.');
            return "";
        }

    };
});

app.filter('hourFormat', function () {
    return function (str) {
        if (str && str.split(":").length >= 2) {
            str = str.split(":")[0] + ":" + str.split(":")[1];
            
            return str;
        }
    }
});
app.filter('dateFormat', function (dateFilter) {
    return function (date, format) {
        if (date != "0000-00-00") {
            if (typeof date == "string") {
                date = new Date(date);
            }

            var d = dateFilter(date, format);
            if (typeof d == "undefined" || d.trim() == "Jan 1, 1970") {
                return "";
            } else {
                return d;
            }
        } else {
            return "";
        }
    }
});
app.filter('elipsisMiddle', function () {
    return function (fullStr, strLen, separator) {
        if (fullStr.length <= strLen)
            return fullStr;

        separator = separator || '...';

        var sepLen = separator.length,
                charsToShow = strLen - sepLen,
                frontChars = Math.ceil(charsToShow / 2),
                backChars = Math.floor(charsToShow / 2);

        return fullStr.substr(0, frontChars) +
                separator +
                fullStr.substr(fullStr.length - backChars);
    };
});
app.filter('countLine', function () {
    return function (input) {
        // do some bounds checking here to ensure it has that index
        var len = input.split(/\r\n|\r|\n/).length;
        return len + " line" + (len - 3 > 1 ? 's' : '');
    }
});
app.filter("timeago", function () {
    //time: the time
    //local: compared to what time? default: now
    //raw: wheter you want in a format of "5 minutes ago", or "5 minutes"
    return function (time, local, raw) {
        var usePlural = false;

        if (!time)
            return "never";

        if (!local) {
            (local = Date.now())
        }

        if (angular.isDate(time)) {
            time = time.getTime();
        } else if (typeof time === "string") {
            time = new Date(time).getTime();
        }

        if (angular.isDate(local)) {
            local = local.getTime();
        } else if (typeof local === "string") {
            local = new Date(local).getTime();
        }

        if (typeof time !== 'number' || typeof local !== 'number') {
            return;
        }

        var
                offset = Math.abs((local - time) / 1000),
                span = [],
                MINUTE = 60,
                HOUR = 3600,
                DAY = 86400,
                WEEK = 604800,
                MONTH = 2629744,
                YEAR = 31556926,
                DECADE = 315569260;

        if (offset <= MINUTE)
            span = ['', raw ? 'now' : 'beberapa saat'];
        else if (offset < (MINUTE * 60))
            span = [Math.round(Math.abs(offset / MINUTE)), 'menit'];
        else if (offset < (HOUR * 24))
            span = [Math.round(Math.abs(offset / HOUR)), 'jam'];
        else if (offset < (DAY * 7))
            span = [Math.round(Math.abs(offset / DAY)), 'hari'];
        else if (offset < (WEEK * 52))
            span = [Math.round(Math.abs(offset / WEEK)), 'minggu'];
        else if (offset < (YEAR * 10))
            span = [Math.round(Math.abs(offset / YEAR)), 'tahun'];
        else if (offset < (DECADE * 100))
            span = [Math.round(Math.abs(offset / DECADE)), 'dekade'];
        else
            span = ['', 'dulu'];

        if (usePlural) {
            span[1] += (span[0] === 0 || span[0] > 1) ? 's' : '';
        }
        span = span.join(' ');

        if (raw === true) {
            return span;
        }
        return (time <= local) ? span + ' yang lalu' : 'pada ' + span;
    }
});
app.directive('contentEdit', ['$timeout', function ($timeout) {
        return {
            restrict: 'A',
            require: '?ngModel',
            link: function (scope, element, attrs, ngModel) {
                // don't do anything unless this is actually bound to a model
                if (!ngModel) {
                    return
                }

                // options
                var opts = {}
                angular.forEach([
                    'stripBr',
                    'noLineBreaks',
                    'selectNonEditable',
                    'moveCaretToEndOnChange',
                ], function (opt) {
                    var o = attrs[opt]
                    opts[opt] = o && o !== 'false'
                })

                // view -> model
                element.bind('input', function (e) {
                    scope.$apply(function () {
                        var html, html2, rerender
                        html = element.html()
                        rerender = false
                        if (opts.stripBr) {
                            html = html.replace(/<br>$/, '')
                        }
                        if (opts.noLineBreaks) {
                            html2 = html.replace(/<div>/g, '').replace(/<br>/g, '').replace(/<\/div>/g, '')
                            if (html2 !== html) {
                                rerender = true
                                html = html2
                            }
                        }
                        ngModel.$setViewValue(html)
                        if (rerender) {
                            ngModel.$render()
                        }
                        if (html === '') {
                            // the cursor disappears if the contents is empty
                            // so we need to refocus
                            $timeout(function () {
                                element[0].blur()
                                element[0].focus()
                            })
                        }
                    })
                })

                // model -> view
                var oldRender = ngModel.$render
                ngModel.$render = function () {
                    var el, el2, range, sel
                    if (!!oldRender) {
                        oldRender()
                    }
                    element.html(ngModel.$viewValue || '')
                    if (opts.moveCaretToEndOnChange) {
                        el = element[0]
                        range = document.createRange()
                        sel = window.getSelection()
                        if (el.childNodes.length > 0) {
                            el2 = el.childNodes[el.childNodes.length - 1]
                            range.setStartAfter(el2)
                        } else {
                            range.setStartAfter(el)
                        }
                        range.collapse(true)
                        sel.removeAllRanges()
                        sel.addRange(range)
                    }
                }
                if (opts.selectNonEditable) {
                    element.bind('click', function (e) {
                        var range, sel, target
                        target = e.toElement
                        if (target !== this && angular.element(target).attr('contenteditable') === 'false') {
                            range = document.createRange()
                            sel = window.getSelection()
                            range.setStartBefore(target)
                            range.setEndAfter(target)
                            sel.removeAllRanges()
                            sel.addRange(range)
                        }
                    })
                }
            }
        }
    }]);
app.directive('modelChange', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            scope.$watch(attrs.ngModel, function (v) {
                $(element.context).trigger('change');
            });
        }
    };
});

app.factory('timestampMarker', [
    function () {
        var timestampMarker = {
            request: function (config) {
                $(".loading").show();
                config.requestTimestamp = new Date().getTime();
                return config;
            },
            response: function (response) {
                $(".loading").hide();
                response.config.responseTimestamp = new Date().getTime();
                return response;
            }
        };
        return timestampMarker;
    }
]);
app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});
app.directive('autoGrow', ['$timeout', '$window', function ($timeout, $window) {
        'use strict';
        var config = {
            append: ''
        };
        return {
            require: 'ngModel',
            restrict: 'A, C',
            link: function (scope, element, attrs, ngModel) {

                // cache a reference to the DOM element
                var ta = element[0],
                        $ta = element;
                // ensure the element is a textarea, and browser is capable
                if (ta.nodeName !== 'TEXTAREA' || !$window.getComputedStyle) {
                    return;
                }

                // set these properties before measuring dimensions
                $ta.css({
                    'overflow': 'hidden',
                    'overflow-y': 'hidden',
                    'word-wrap': 'break-word'
                });
                // force text reflow
                var text = ta.value;
                ta.value = '';
                ta.value = text;
                var appendText = attrs.msdElastic || config.append,
                        append = appendText === '\\n' ? '\n' : appendText,
                        $win = angular.element($window),
                        mirrorStyle = 'position: absolute; top: -999px; right: auto; bottom: auto; left: 0 ;' +
                        'overflow: hidden; -webkit-box-sizing: content-box;' +
                        '-moz-box-sizing: content-box; box-sizing: content-box;' +
                        'min-height: 0 !important; height: 0 !important; padding: 0;' +
                        'word-wrap: break-word; border: 0;',
                        $mirror = angular.element('<textarea tabindex="-1" ' +
                                'style="' + mirrorStyle + '"/>').data('elastic', true),
                        mirror = $mirror[0],
                        taStyle = getComputedStyle(ta), resize = taStyle.getPropertyValue('resize'),
                        borderBox = taStyle.getPropertyValue('box-sizing') === 'border-box' ||
                        taStyle.getPropertyValue('-moz-box-sizing') === 'border-box' || taStyle.getPropertyValue('-webkit-box-sizing') === 'border-box',
                        boxOuter = !borderBox ? {width: 0, height: 0} : {
                    width: parseInt(taStyle.getPropertyValue('border-right-width'), 10) +
                            parseInt(taStyle.getPropertyValue('padding-right'), 10) +
                            parseInt(taStyle.getPropertyValue('padding-left'), 10) + parseInt(taStyle.getPropertyValue('border-left-width'), 10),
                    height: parseInt(taStyle.getPropertyValue('border-top-width'), 10) +
                            parseInt(taStyle.getPropertyValue('padding-top'), 10) +
                            parseInt(taStyle.getPropertyValue('padding-bottom'), 10) +
                            parseInt(taStyle.getPropertyValue('border-bottom-width'), 10)
                },
                minHeightValue = parseInt(taStyle.getPropertyValue('min-height'), 10),
                        heightValue = parseInt(taStyle.getPropertyValue('height'), 10),
                        minHeight = Math.max(minHeightValue, heightValue) - boxOuter.height,
                        maxHeight = parseInt(taStyle.getPropertyValue('max-height'), 10),
                        mirrored,
                        active,
                        copyStyle = ['font-family',
                            'font-size',
                            'font-weight',
                            'font-style',
                            'letter-spacing',
                            'line-height',
                            'text-transform',
                            'word-spacing',
                            'text-indent'];
                // exit if elastic already applied (or is the mirror element)
                if ($ta.data('elastic')) {
                    return;
                }

                // Opera returns max-height of -1 if not set
                maxHeight = maxHeight && maxHeight > 0 ? maxHeight : 9e4;
                // append mirror to the DOM
                if (mirror.parentNode !== document.body) {
                    angular.element(document.body).append(mirror);
                }

                // set resize and apply elastic
                $ta.css({
                    'resize': (resize === 'none' || resize === 'vertical') ? 'none' : 'horizontal'
                }).data('elastic', true);
                /*
                 * methods
                 */

                function initMirror() {
                    mirrored = ta;
                    // copy the essential styles from the textarea to the mirror
                    taStyle = getComputedStyle(ta);
                    angular.forEach(copyStyle, function (val) {
                        mirrorStyle += val + ':' + taStyle.getPropertyValue(val) + ';';
                    });
                    mirror.setAttribute('style', mirrorStyle);
                }

                function adjust() {
                    var taHeight,
                            taComputedStyleWidth,
                            mirrorHeight,
                            width,
                            overflow;
                    if (mirrored !== ta) {
                        initMirror();
                    }

                    // active flag prevents actions in function from calling adjust again
                    if (!active) {
                        active = true;
                        mirror.value = ta.value + append; // optional whitespace to improve animation
                        mirror.style.overflowY = ta.style.overflowY;
                        taHeight = ta.style.height === '' ? 'auto' : parseInt(ta.style.height, 10);
                        taComputedStyleWidth = getComputedStyle(ta).getPropertyValue('width');
                        // ensure getComputedStyle has returned a readable 'used value' pixel width
                        if (taComputedStyleWidth.substr(taComputedStyleWidth.length - 2, 2) === 'px') {
                            // update mirror width in case the textarea width has changed
                            width = parseInt(taComputedStyleWidth, 10) - boxOuter.width;
                            mirror.style.width = width + 'px';
                        }

                        mirrorHeight = mirror.scrollHeight;
                        if (mirrorHeight > maxHeight) {
                            mirrorHeight = maxHeight;
                            overflow = 'scroll';
                        } else if (mirrorHeight < minHeight) {
                            mirrorHeight = minHeight;
                        }
                        mirrorHeight += boxOuter.height;
                        ta.style.overflowY = overflow || 'hidden';
                        if (taHeight !== mirrorHeight) {
                            ta.style.height = mirrorHeight + 'px';
                            scope.$emit('elastic:resize', $ta);
                        }

                        // small delay to prevent an infinite loop
                        $timeout(function () {
                            active = false;
                        }, 1);
                    }
                }

                function forceAdjust() {
                    active = false;
                    adjust();
                }

                /*
                 * initialise
                 */

                // listen
                if ('onpropertychange' in ta && 'oninput' in ta) {
                    // IE9
                    ta['oninput'] = ta.onkeyup = adjust;
                } else {
                    ta['oninput'] = adjust;
                }

                $win.bind('resize', forceAdjust);
                scope.$watch(function () {
                    return ngModel.$modelValue;
                }, function (newValue) {
                    forceAdjust();
                });
                scope.$on('elastic:adjust', function () {
                    forceAdjust();
                });
                $timeout(adjust);
                /*
                 * destroy
                 */

                scope.$on('$destroy', function () {
                    $mirror.remove();
                    $win.unbind('resize', forceAdjust);
                });
            }
        };
    }
]);
app.directive('dynamic', function ($compile) {
    return {
        restrict: 'A',
        replace: true,
        link: function (scope, ele, attrs) {
            scope.$watch(attrs.dynamic, function (html) {
                ele.html(html);
                $compile(ele.contents())(scope);
            });
        }
    };
});
app.config(['$httpProvider',
    function ($httpProvider) {
        $httpProvider.interceptors.push('timestampMarker');
    }
]);
app.directive('expandAttributes', function ($parse) {
    return function ($scope, $element, $attrs) {
        var attrs = $parse($attrs.expandAttributes)($scope);
        for (var attrName in attrs) {
            $attrs.$set(attrName, attrs[attrName]);
        }
    }
})
app.directive('splitPane', function ($window) {
    return function (scope, element, attr) {
        var $hpane = $(element).find(".hpane");
        $hpane.each(function (i, k) {
            if (i < $hpane.length - 1) {
                var that = this;
                $('<div class="hpane-resizer"></div>').draggable({
                    axis: "x",
                    start: function (e, ui) {
                        $(this).css("position", 'absolute');
                        this.width = $(that).width();
                    },
                    drag: function (e, ui) {
                        $(that).width(this.width + ui.position.left);
                    }
                }).insertAfter($(that));
            }
        });
    };
});
app.directive('ngDelay', ['$timeout',
    function ($timeout) {
        return {
            restrict: 'A',
            scope: true,
            compile: function (element, attributes) {
                var expression = attributes['ngChange'];
                if (!expression)
                    return;
                var ngModel = attributes['ngModel'];
                if (ngModel)
                    attributes['ngModel'] = '$parent.' + ngModel;
                attributes['ngChange'] = '$$delay.execute()';
                return {
                    post: function (scope, element, attributes) {
                        scope.$$delay = {
                            expression: expression,
                            delay: scope.$eval(attributes['ngDelay']),
                            execute: function () {
                                var state = scope.$$delay;
                                state.then = Date.now();
                                $timeout(function () {
                                    if (Date.now() - state.then >= state.delay) {
                                        scope.$parent.$eval(expression);
                                    }
                                }, state.delay);
                            }
                        };
                    }
                }
            }
        };
    }
]);
app.directive("formSubmit", ['$timeout', function ($timeout) {
        return {
            scope: {
                formSubmit: "@"
            },
            link: function (scope, element, attributes) {
                element.bind("submit", function (loadEvent) {

                    scope.$parent.$eval(scope.formSubmit);

                    element.unbind("submit");
                    $timeout(function () {
                        element.submit();
                    }, 0);
                    return false;
                });
            }
        }
    }]);