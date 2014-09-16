if (!Array.prototype.filter) {
    Array.prototype.filter = function(fn, context) {
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
    Object.getProperty = function(obj, path, def) {

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
/** jQuery Caret **/
(function($) {
    // Behind the scenes method deals with browser
    // idiosyncrasies and such
    $.caretTo = function(el, index) {
        if (el.createTextRange) {
            var range = el.createTextRange();
            range.move("character", index);
            range.select();
        } else if (el.selectionStart != null) {
            el.focus();
            el.setSelectionRange(index, index);
        }
    };

    // Set caret to a particular index
    $.fn.setCaretPosition = function(index, offset) {
        return this.queue(function(next) {
            if (isNaN(index)) {
                var i = $(this).val().indexOf(index);

                if (offset === true) {
                    i += index.length;
                } else if (offset) {
                    i += offset;
                }

                $.caretTo(this, i);
            } else {
                $.caretTo(this, index);
            }

            next();
        });
    };

    $.fn.getCaretPosition = function() {
        var input = this.get(0);
        if (!input)
            return; // No (input) element found
        if ('selectionStart' in input) {
            // Standard-compliant browsers
            return input.selectionStart;
        } else if (document.selection) {
            // IE
            input.focus();
            var sel = document.selection.createRange();
            var selLen = document.selection.createRange().text.length;
            sel.moveStart('character', -input.value.length);
            return sel.text.length - selLen;
        }
    }
})(jQuery);

var controllerProvider = null;
var app = angular.module("main", [
    'ui.layout', 
    'ui.tree', 
    'ui.bootstrap', 
    'ngGrid', 
    'angularFileUpload',
    'ngStorage'
]);
app.config(function($sceProvider, $controllerProvider) {
    controllerProvider = $controllerProvider;
    $sceProvider.enabled(false);
});
app.filter('capitalize', function() {
    return function(input, scope) {
        if (input != null)
            input = input.toLowerCase();
        return input.substring(0, 1).toUpperCase() + input.substring(1);
    }
});
app.directive('modelChange', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            scope.$watch(attrs.ngModel, function(v) {
                $(element.context).trigger('change');
            });
        }
    };
});
app.factory('timestampMarker', [
    function() {
        var timestampMarker = {
            request: function(config) {
                $(".loading").show();
                config.requestTimestamp = new Date().getTime();
                return config;
            },
            response: function(response) {
                $(".loading").hide();
                response.config.responseTimestamp = new Date().getTime();
                return response;
            }
        };
        return timestampMarker;
    }
]);
app.directive('ngEnter', function() {
    return function(scope, element, attrs) {
        element.bind("keydown keypress", function(event) {
            if (event.which === 13) {
                scope.$apply(function() {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});
app.directive('autoGrow', ['$timeout', '$window', function($timeout, $window) {
        'use strict';
        var config = {
            append: ''
        };
        return {
            require: 'ngModel',
            restrict: 'A, C',
            link: function(scope, element, attrs, ngModel) {

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
                    angular.forEach(copyStyle, function(val) {
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
                        $timeout(function() {
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
                scope.$watch(function() {
                    return ngModel.$modelValue;
                }, function(newValue) {
                    forceAdjust();
                });
                scope.$on('elastic:adjust', function() {
                    forceAdjust();
                });
                $timeout(adjust);
                /*
                 * destroy
                 */

                scope.$on('$destroy', function() {
                    $mirror.remove();
                    $win.unbind('resize', forceAdjust);
                });
            }
        };
    }
]);
app.directive('dynamic', function($compile) {
    return {
        restrict: 'A',
        replace: true,
        link: function(scope, ele, attrs) {
            scope.$watch(attrs.dynamic, function(html) {
                ele.html(html);
                $compile(ele.contents())(scope);
            });
        }
    };
});
app.config(['$httpProvider',
    function($httpProvider) {
        $httpProvider.interceptors.push('timestampMarker');
    }
]);
app.directive('expandAttributes', function($parse) {
    return function($scope, $element, $attrs) {
        var attrs = $parse($attrs.expandAttributes)($scope);
        for (var attrName in attrs) {
            $attrs.$set(attrName, attrs[attrName]);
        }
    }
})
app.directive('splitPane', function($window) {
    return function(scope, element, attr) {
        var $hpane = $(element).find(".hpane");
        $hpane.each(function(i, k) {
            if (i < $hpane.length - 1) {
                var that = this;
                $('<div class="hpane-resizer"></div>').draggable({
                    axis: "x",
                    start: function(e, ui) {
                        $(this).css("position", 'absolute');
                        this.width = $(that).width();
                    },
                    drag: function(e, ui) {
                        $(that).width(this.width + ui.position.left);
                    }
                }).insertAfter($(that));
            }
        });
    };
});
app.directive('ngDelay', ['$timeout',
    function($timeout) {
        return {
            restrict: 'A',
            scope: true,
            compile: function(element, attributes) {
                var expression = attributes['ngChange'];
                if (!expression)
                    return;
                var ngModel = attributes['ngModel'];
                if (ngModel)
                    attributes['ngModel'] = '$parent.' + ngModel;
                attributes['ngChange'] = '$$delay.execute()';
                return {
                    post: function(scope, element, attributes) {
                        scope.$$delay = {
                            expression: expression,
                            delay: scope.$eval(attributes['ngDelay']),
                            execute: function() {
                                var state = scope.$$delay;
                                state.then = Date.now();
                                $timeout(function() {
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
app.directive("formSubmit", ['$timeout', function($timeout) {
        return {
            scope: {
                formSubmit: "@"
            },
            link: function(scope, element, attributes) {
                element.bind("submit", function(loadEvent) {
                    
                    scope.$parent.$eval(scope.formSubmit);

                    element.unbind("submit");
                    $timeout(function() {
                        element.submit();
                    }, 0);
                    return false;
                });
            }
        }
    }]);