/* global angular, plansys, Yii */

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
    'ui.bootstrap',
    'ngStorage',
    'oc.lazyLoad'
]);
app.config(function ($sceProvider, $controllerProvider, $provide) {
    $provide.decorator('$browser', ['$delegate', function ($delegate) {
        $delegate.onUrlChange = function () {
        };
        $delegate.url = function () {
            return "";
        };
        return $delegate;
    }
    ]);
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

app.filter('repoPath', function() {
    return function(input, scope) {
        if (window.plansys && window.plansys.repoPath) {
            if (input.indexOf(window.plansys.repoPath) === 0) {
                if (window.plansys.repoPath.indexOf(window.plansys.rootPath) == 0) {
                    var repoDir = window.plansys.repoPath.substr(window.plansys.rootPath.length);
                    return window.plansys.baseUrl + repoDir + input.substr(window.plansys.repoPath.length);
                } else {
                    return window.plansys.baseUrl + input.substr(window.plansys.repoPath.length);
                }
            }
        }
        
        return input;
    }
});
app.filter('relativePath', function() {
    return function(input, scope) {
        if (window.plansys && window.plansys.rootPath) {
            if (input.indexOf(window.plansys.rootPath) === 0) {
                return window.plansys.baseUrl + input.substr(window.plansys.rootPath.length);
            }
        }
        
        return input;
    }
});
app.filter('ucfirst', function () {
    return function (input, arg) {
        return ucfirst(input);
    };
});
app.filter('fileSize', function () {
    return function (size, precision) {

        if (precision == 0 || precision == null) {
            precision = 1;
        }
        if (size == 0 || size == null) {
            return "";
        } else if (!isNaN(size)) {
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
    return function (input, format) {
        if (!format && plansys && plansys.dateFormat) {
            format = plansys.dateFormat;
        }
        
        if (!format || format === null) {
            format = "date";
        }
        
        if (format == "date") {
            format = plansys.dateFormat;
            if (!format) {
                format = 'd M Y';
            }
        }
        
        if (format == "time") {
            format = plansys.timeFormat;
            if (!format) {
                format = 'H:i';
            }
        }
        
        if (format == "datetime") {
            format = plansys.dateTimeFormat;
            if (!format) {
                format = 'd M Y - H:i';
            }

        }

        if (input != "0000-00-00" && !!input && input != null) {
            return date(format, strtotime(input + ""));
        } else {
            return "";
        }
    }
});
app.filter('more', function() {
    return function (input, len) {
        if (input) {
            if (input.length > len) {
                return input.substring(0, len).trim() + '...';
            }
        }
        return input;
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
        if (typeof input != 'string')
            return 0;

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
        var usePlural = true;

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
            span = ['', raw ? 'now' : 'a minute'];
        else if (offset < (MINUTE * 60))
            span = [Math.round(Math.abs(offset / MINUTE)), 'minute'];
        else if (offset < (HOUR * 24))
            span = [Math.round(Math.abs(offset / HOUR)), 'hour'];
        else if (offset < (DAY * 7))
            span = [Math.round(Math.abs(offset / DAY)), 'day'];
        else if (offset < (WEEK * 52))
            span = [Math.round(Math.abs(offset / WEEK)), 'week'];
        else if (offset < (YEAR * 10))
            span = [Math.round(Math.abs(offset / YEAR)), 'year'];
        else if (offset < (DECADE * 100))
            span = [Math.round(Math.abs(offset / DECADE)), 'decade'];
        else
            span = ['', 'a long time'];

        if (usePlural) {
            span[1] += (span[0] === 0 || span[0] > 1) ? 's' : '';
        }
        span = span.join(' ');

        if (raw === true) {
            return span;
        }
        return (time <= local) ? span + ' ago' : 'in ' + span;
    }
});
app.directive("contenteditable", function ($timeout) {
    return {
        restrict: "A",
        require: "ngModel",
        link: function (scope, element, attrs, ngModel) {

            function read() {
                var html = element.text().trim();
                ngModel.$setViewValue(html);
            }

            ngModel.$render = function () {
                element.html(ngModel.$viewValue || "");
            };

            element.bind("blur", function (e) {
                scope.$apply(read);
            });
        }
    };
});

//String.prototype.fromMysqlDate = String.prototype.fromMysqlDate ||  function () {
//    var t = this.split(/[- :]/);
//    return new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
//}

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
app.directive('ngUrl', function ($interpolate) {
    return {
        link: function ($scope, el, attrs) {
            attrs.$observe(
                "ngUrl",
                function (newValue, oldValue) {
                    $(el).attr('href', Yii.app.createUrl($interpolate(newValue)($scope.$parent)));
                }
            );
        }
    };
});
app.directive('bindHtmlCompile', ['$compile', function ($compile) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            scope.$watch(function () {
                return scope.$eval(attrs.bindHtmlCompile);
            }, function (value) {
                // In case value is a TrustedValueHolderType, sometimes it
                // needs to be explicitly called into a string in order to
                // get the HTML string.
                element.html(value && value.toString());
                // If scope is provided use it, otherwise use parent scope
                var compileScope = scope;
                if (attrs.bindHtmlScope) {
                    compileScope = scope.$eval(attrs.bindHtmlScope);
                }
                $compile(element.contents())(compileScope);
            });
        }
    };
}]);

app.directive('ngAssign', function($timeout) {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function ($scope, el, attrs, ngModel) {
            el.assignWatcher = $scope.$watch(attrs.ngAssign, function(n, o) {
                if (!!n && !!n.toString) {
                    $scope.$eval(attrs.ngModel + ' = "' + n.toString() + '"' );
                }
            },true);
            
        }
    }; 
});
app.directive('autoGrow', ['$timeout', '$window', function ($timeout, $window) {
    'use strict';
    var config = {
        append: ''
    };
    return {
        require: '?ngModel',
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
app.directive("formSubmit", ['$timeout',
    function ($timeout) {
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
    }
]);

//PHP date implementation in JS
function date(format, timestamp) {
    var that = this;
    var jsdate, f;
    var txt_words = ['Sun', 'Mon', 'Tues', 'Wednes', 'Thurs', 'Fri', 'Satur', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var formatChr = /\\?(.?)/gi;
    var formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s
    };
    var _pad = function (n, c) {
        n = String(n);
        while (n.length < c) {
            n = '0' + n
        }
        return n
    };
    f = {
        d: function () {
            return _pad(f.j(), 2)
        }, D: function () {
            return f.l().slice(0, 3)
        }, j: function () {
            return jsdate.getDate()
        }, l: function () {
            return txt_words[f.w()] + 'day'
        }, N: function () {
            return f.w() || 7
        }, S: function () {
            var j = f.j();
            var i = j % 10;
            if (i <= 3 && parseInt((j % 100) / 10, 10) == 1) {
                i = 0
            }
            return ['st', 'nd', 'rd'][i - 1] || 'th'
        }, w: function () {
            return jsdate.getDay()
        }, z: function () {
            var a = new Date(f.Y(), f.n() - 1, f.j());
            var b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 864e5)
        }, W: function () {
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3);
            var b = new Date(a.getFullYear(), 0, 4);
            return _pad(1 + Math.round((a - b) / 864e5 / 7), 2)
        }, F: function () {
            return txt_words[6 + f.n()]
        }, m: function () {
            return _pad(f.n(), 2)
        }, M: function () {
            return f.F().slice(0, 3)
        }, n: function () {
            return jsdate.getMonth() + 1
        }, t: function () {
            return (new Date(f.Y(), f.n(), 0)).getDate()
        }, L: function () {
            var j = f.Y();
            return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0
        }, o: function () {
            var n = f.n();
            var W = f.W();
            var Y = f.Y();
            return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0)
        }, Y: function () {
            return jsdate.getFullYear()
        }, y: function () {
            return f.Y().toString().slice(-2)
        }, a: function () {
            return jsdate.getHours() > 11 ? 'pm' : 'am'
        }, A: function () {
            return f.a().toUpperCase()
        }, B: function () {
            var H = jsdate.getUTCHours() * 36e2;
            var i = jsdate.getUTCMinutes() * 60;
            var s = jsdate.getUTCSeconds();
            return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3)
        }, g: function () {
            return f.G() % 12 || 12
        }, G: function () {
            return jsdate.getHours()
        }, h: function () {
            return _pad(f.g(), 2)
        }, H: function () {
            return _pad(f.G(), 2)
        }, i: function () {
            return _pad(jsdate.getMinutes(), 2)
        }, s: function () {
            return _pad(jsdate.getSeconds(), 2)
        }, u: function () {
            return _pad(jsdate.getMilliseconds() * 1000, 6)
        }, e: function () {
            throw'Not supported (see source code of date() for timezone on how to add support)'
        }, I: function () {
            var a = new Date(f.Y(), 0);
            var c = Date.UTC(f.Y(), 0);
            var b = new Date(f.Y(), 6);
            var d = Date.UTC(f.Y(), 6);
            return ((a - c) !== (b - d)) ? 1 : 0
        }, O: function () {
            var tzo = jsdate.getTimezoneOffset();
            var a = Math.abs(tzo);
            return (tzo > 0 ? '-' : '+') + _pad(Math.floor(a / 60) * 100 + a % 60, 4)
        }, P: function () {
            var O = f.O();
            return (O.substr(0, 3) + ':' + O.substr(3, 2))
        }, T: function () {
            return 'UTC'
        }, Z: function () {
            return -jsdate.getTimezoneOffset() * 60
        }, c: function () {
            return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb)
        }, r: function () {
            return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb)
        }, U: function () {
            return jsdate / 1000 | 0
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = (timestamp === undefined ? new Date() : (timestamp instanceof Date) ? new Date(timestamp) : new Date(timestamp * 1000));

        return format.replace(formatChr, formatChrCb)
    };
    return this.date(format, timestamp)
}

//PHP str_replace implementation in JS
function str_replace(s, r, string) {
    if (s.constructor === Array) {
        for (var i = 0; i < s.length; i++) {
            string = string.replace(s[i], r[i]);
        }
    } else {
        string = string.replace(s, r);
    }
    return string;
}

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

//PHP ucfirst implementation in JS
function ucfirst(input) {
    return input.replace(/(?:^|\s)\S/g, function (a) {
        return a.toUpperCase();
    })
}

//PHP strtotime implementation in JS
function strtotime(text, now) {
    var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;
    if (!text) {
        return fail
    }
    text = text.replace(/^\s+|\s+$/g, '').replace(/\s{2,}/g, ' ').replace(/[\t\r\n]/g, '').toLowerCase();
    match = text.match(/^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);
    if (match && match[2] === match[4]) {
        if (match[1] > 1901) {
            switch (match[2]) {
                case'-':
                {
                    if (match[3] > 12 || match[5] > 31) {
                        return fail
                    }
                    return new Date(match[1], parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
                case'.':
                {
                    return fail
                }
                case'/':
                {
                    if (match[3] > 12 || match[5] > 31) {
                        return fail
                    }
                    return new Date(match[1], parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
            }
        } else if (match[5] > 1901) {
            switch (match[2]) {
                case'-':
                {
                    if (match[3] > 12 || match[1] > 31) {
                        return fail
                    }
                    return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
                case'.':
                {
                    if (match[3] > 12 || match[1] > 31) {
                        return fail
                    }
                    return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
                case'/':
                {
                    if (match[1] > 12 || match[3] > 31) {
                        return fail
                    }
                    return new Date(match[5], parseInt(match[1], 10) - 1, match[3], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
            }
        } else {
            switch (match[2]) {
                case'-':
                {
                    if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
                        return fail
                    }
                    year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
                    return new Date(year, parseInt(match[3], 10) - 1, match[5], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
                case'.':
                {
                    if (match[5] >= 70) {
                        if (match[3] > 12 || match[1] > 31) {
                            return fail
                        }
                        return new Date(match[5], parseInt(match[3], 10) - 1, match[1], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                    }
                    if (match[5] < 60 && !match[6]) {
                        if (match[1] > 23 || match[3] > 59) {
                            return fail
                        }
                        today = new Date();
                        return new Date(today.getFullYear(), today.getMonth(), today.getDate(), match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000
                    }
                    return fail
                }
                case'/':
                {
                    if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
                        return fail
                    }
                    year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
                    return new Date(year, parseInt(match[1], 10) - 1, match[3], match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
                }
                case':':
                {
                    if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
                        return fail
                    }
                    today = new Date();
                    return new Date(today.getFullYear(), today.getMonth(), today.getDate(), match[1] || 0, match[3] || 0, match[5] || 0) / 1000
                }
            }
        }
    }
    if (text === 'now') {
        return now === null || isNaN(now) ? new Date().getTime() / 1000 | 0 : now | 0
    }
    if (!isNaN(parsed = Date.parse(text))) {
        return parsed / 1000 | 0
    }
    date = now ? new Date(now * 1000) : new Date();
    days = {'sun': 0, 'mon': 1, 'tue': 2, 'wed': 3, 'thu': 4, 'fri': 5, 'sat': 6};
    ranges = {'yea': 'FullYear', 'mon': 'Month', 'day': 'Date', 'hou': 'Hours', 'min': 'Minutes', 'sec': 'Seconds'};
    function lastNext(type, range, modifier) {
        var diff, day = days[range];
        if (typeof day !== 'undefined') {
            diff = day - date.getDay();
            if (diff === 0) {
                diff = 7 * modifier
            } else if (diff > 0 && type === 'last') {
                diff -= 7
            } else if (diff < 0 && type === 'next') {
                diff += 7
            }
            date.setDate(date.getDate() + diff)
        }
    }

    function process(val) {
        var splt = val.split(' '), type = splt[0], range = splt[1].substring(0, 3), typeIsNumber = /\d+/.test(type), ago = splt[2] === 'ago', num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);
        if (typeIsNumber) {
            num *= parseInt(type, 10)
        }
        if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
            return date['set' + ranges[range]](date['get' + ranges[range]]() + num)
        }
        if (range === 'wee') {
            return date.setDate(date.getDate() + (num * 7))
        }
        if (type === 'next' || type === 'last') {
            lastNext(type, range, num)
        } else if (!typeIsNumber) {
            return false
        }
        return true
    }

    times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' + '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' + '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
    regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';
    match = text.match(new RegExp(regex, 'gi'));
    if (!match) {
        return fail
    }
    for (i = 0, len = match.length; i < len; i++) {
        if (!process(match[i])) {
            return fail
        }
    }
    return (date.getTime() / 1000)
}
function trim(str, charlist) {
    //  discuss at: http://phpjs.org/functions/trim/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: mdsjack (http://www.mdsjack.bo.it)
    // improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Steven Levithan (http://blog.stevenlevithan.com)
    // improved by: Jack
    //    input by: Erkekjetter
    //    input by: DxGx
    // bugfixed by: Onno Marsman
    //   example 1: trim('    Kevin van Zonneveld    ');
    //   returns 1: 'Kevin van Zonneveld'
    //   example 2: trim('Hello World', 'Hdle');
    //   returns 2: 'o Wor'
    //   example 3: trim(16, 1);
    //   returns 3: 6

    var whitespace, l = 0,
        i = 0;
    str += '';

    if (!charlist) {
        // default list
        whitespace =
            ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    }

    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }

    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }

    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

if (!Array.isArray) {
    Array.isArray = function (arg) {
        return Object.prototype.toString.call(arg) === '[object Array]';
    };
}