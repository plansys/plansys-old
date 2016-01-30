(function () {

    var phpJS = function() {

    };

    phpJS.prototype.krsort = function (inputArr, sort_flags) {
        // http://kevin.vanzonneveld.net
        // +   original by: GeekFG (http://geekfg.blogspot.com)
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Brett Zamir (http://brett-zamir.me)
        // %          note 1: The examples are correct, this is a new way
        // %        note 2: This function deviates from PHP in returning a copy of the array instead
        // %        note 2: of acting by reference and returning true; this was necessary because
        // %        note 2: IE does not allow deleting and re-adding of properties without caching
        // %        note 2: of property position; you can set the ini of "phpjs.strictForIn" to true to
        // %        note 2: get the PHP behavior, but use this only if you are in an environment
        // %        note 2: such as Firefox extensions where for-in iteration order is fixed and true
        // %        note 2: property deletion is supported. Note that we intend to implement the PHP
        // %        note 2: behavior by default if IE ever does allow it; only gives shallow copy since
        // %        note 2: is by reference in PHP anyways
        // %        note 3: Since JS objects' keys are always strings, and (the
        // %        note 3: default) SORT_REGULAR flag distinguishes by key type,
        // %        note 3: if the content is a numeric string, we treat the
        // %        note 3: "original type" as numeric.
        // -    depends on: i18n_loc_get_default
        // *     example 1: data = {d: 'lemon', a: 'orange', b: 'banana', c: 'apple'};
        // *     example 1: data = krsort(data);
        // *     results 1: {d: 'lemon', c: 'apple', b: 'banana', a: 'orange'}
        // *     example 2: ini_set('phpjs.strictForIn', true);
        // *     example 2: data = {2: 'van', 3: 'Zonneveld', 1: 'Kevin'};
        // *     example 2: krsort(data);
        // *     results 2: data == {3: 'Kevin', 2: 'van', 1: 'Zonneveld'}
        // *     returns 2: true
        var tmp_arr = {},
            keys = [],
            sorter, i, k, that = this,
            strictForIn = false,
            populateArr = {};

        switch (sort_flags) {
            case 'SORT_STRING':
                // compare items as strings
                sorter = function (a, b) {
                    return that.strnatcmp(b, a);
                };
                break;
            case 'SORT_LOCALE_STRING':
                // compare items as strings, based on the current locale (set with  i18n_loc_set_default() as of PHP6)
                var loc = this.i18n_loc_get_default();
                sorter = this.php_js.i18nLocales[loc].sorting;
                break;
            case 'SORT_NUMERIC':
                // compare items numerically
                sorter = function (a, b) {
                    return (b - a);
                };
                break;
            case 'SORT_REGULAR':
            // compare items normally (don't change types)
            default:
                sorter = function (b, a) {
                    var aFloat = parseFloat(a),
                        bFloat = parseFloat(b),
                        aNumeric = aFloat + '' === a,
                        bNumeric = bFloat + '' === b;
                    if (aNumeric && bNumeric) {
                        return aFloat > bFloat ? 1 : aFloat < bFloat ? -1 : 0;
                    } else if (aNumeric && !bNumeric) {
                        return 1;
                    } else if (!aNumeric && bNumeric) {
                        return -1;
                    }
                    return a > b ? 1 : a < b ? -1 : 0;
                };
                break;
        }

        // Make a list of key names
        for (k in inputArr) {
            if (inputArr.hasOwnProperty(k)) {
                keys.push(k);
            }
        }
        keys.sort(sorter);

        // BEGIN REDUNDANT
        this.php_js = this.php_js || {};
        this.php_js.ini = this.php_js.ini || {};
        // END REDUNDANT
        strictForIn = this.php_js.ini['phpjs.strictForIn'] && this.php_js.ini['phpjs.strictForIn'].local_value && this.php_js.ini['phpjs.strictForIn'].local_value !== 'off';
        populateArr = strictForIn ? inputArr : populateArr;


        // Rebuild array with sorted key names
        for (i = 0; i < keys.length; i++) {
            k = keys[i];
            tmp_arr[k] = inputArr[k];
            if (strictForIn) {
                delete inputArr[k];
            }
        }
        for (i in tmp_arr) {
            if (tmp_arr.hasOwnProperty(i)) {
                populateArr[i] = tmp_arr[i];
            }
        }

        return strictForIn || populateArr;
    }

    phpJS.prototype.ini_set = function (varname, newvalue) {
        // http://kevin.vanzonneveld.net
        // +   original by: Brett Zamir (http://brett-zamir.me)
        // %        note 1: This will not set a global_value or access level for the ini item
        // *     example 1: ini_set('date.timezone', 'America/Chicago');
        // *     returns 1: 'Asia/Hong_Kong'

        var oldval = '',
            that = this;
        this.php_js = this.php_js || {};
        this.php_js.ini = this.php_js.ini || {};
        this.php_js.ini[varname] = this.php_js.ini[varname] || {};
        oldval = this.php_js.ini[varname].local_value;

        var _setArr = function (oldval) { // Although these are set individually, they are all accumulated
            if (typeof oldval === 'undefined') {
                that.php_js.ini[varname].local_value = [];
            }
            that.php_js.ini[varname].local_value.push(newvalue);
        };

        switch (varname) {
            case 'extension':
                if (typeof this.dl === 'function') {
                    this.dl(newvalue); // This function is only experimental in php.js
                }
                _setArr(oldval, newvalue);
                break;
            default:
                this.php_js.ini[varname].local_value = newvalue;
                break;
        }
        return oldval;
    }

    phpJS.prototype.strtr = function (str, from, to) {
        // http://kevin.vanzonneveld.net
        // +   original by: Brett Zamir (http://brett-zamir.me)
        // +      input by: uestla
        // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +      input by: Alan C
        // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +      input by: Taras Bogach
        // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
        // +      input by: jpfle
        // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
        // -   depends on: krsort
        // -   depends on: ini_set
        // *     example 1: $trans = {'hello' : 'hi', 'hi' : 'hello'};
        // *     example 1: strtr('hi all, I said hello', $trans)
        // *     returns 1: 'hello all, I said hi'
        // *     example 2: strtr('äaabaåccasdeöoo', 'äåö','aao');
        // *     returns 2: 'aaabaaccasdeooo'
        // *     example 3: strtr('ääääääää', 'ä', 'a');
        // *     returns 3: 'aaaaaaaa'
        // *     example 4: strtr('http', 'pthxyz','xyzpth');
        // *     returns 4: 'zyyx'
        // *     example 5: strtr('zyyx', 'pthxyz','xyzpth');
        // *     returns 5: 'http'
        // *     example 6: strtr('aa', {'a':1,'aa':2});
        // *     returns 6: '2'
        var fr = '',
            i = 0,
            j = 0,
            lenStr = 0,
            lenFrom = 0,
            tmpStrictForIn = false,
            fromTypeStr = '',
            toTypeStr = '',
            istr = '';
        var tmpFrom = [];
        var tmpTo = [];
        var ret = '';
        var match = false;

        // Received replace_pairs?
        // Convert to normal from->to chars
        if (typeof from === 'object') {
            tmpStrictForIn = this.ini_set('phpjs.strictForIn', false); // Not thread-safe; temporarily set to true
            from = this.krsort(from);
            this.ini_set('phpjs.strictForIn', tmpStrictForIn);

            for (fr in from) {
                if (from.hasOwnProperty(fr)) {
                    tmpFrom.push(fr);
                    tmpTo.push(from[fr]);
                }
            }

            from = tmpFrom;
            to = tmpTo;
        }

        // Walk through subject and replace chars when needed
        lenStr = str.length;
        lenFrom = from.length;
        fromTypeStr = typeof from === 'string';
        toTypeStr = typeof to === 'string';

        for (i = 0; i < lenStr; i++) {
            match = false;
            if (fromTypeStr) {
                istr = str.charAt(i);
                for (j = 0; j < lenFrom; j++) {
                    if (istr == from.charAt(j)) {
                        match = true;
                        break;
                    }
                }
            } else {
                for (j = 0; j < lenFrom; j++) {
                    if (str.substr(i, from[j].length) == from[j]) {
                        match = true;
                        // Fast forward
                        i = (i + from[j].length) - 1;
                        break;
                    }
                }
            }
            if (match) {
                ret += toTypeStr ? to.charAt(j) : to[j];
            } else {
                ret += str.charAt(i);
            }
        }

        return ret;
    }

    this['phpJS'] = new phpJS();
}).call(this);

Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

var UrlManager = (function () {

    var GET_FORMAT = 'get';
    var PATH_FORMAT = 'path';

    var urlManager = function (opts) {
        this.opts = {
            rules: {},
            urlSuffix: '',
            showScriptName: true,
            appendParams: true,
            routeVar: 'r',

            caseSensitive: true,
            matchValue: false,
            useStrictParsing: true,

            urlFormat: GET_FORMAT
        };

        for (var attr in opts) {
            this.opts[attr] = opts[attr];
        }

        this._rules = [];

        this.processRules();
    }


    urlManager.prototype.processRules = function () {
        if (Object.size(this.opts.rules) === 0 || this.urlFormat === GET_FORMAT) {
            return;
        }

        for (var pattern in this.opts.rules) {
            var route = this.opts.rules[pattern];
            this._rules.push(this.createUrlRule(route, pattern));
        }
    };

    urlManager.prototype.addRules = function (rules, append) {
        append = typeof append !== 'undefined' ? append : true;
        if (append) {
            for (var pattern in this.opts.rules) {
                var route = this.opts.rules[pattern];
                this._rules.push(this.createUrlRule(route, pattern));
            }
        } else {
            rules.reverse();
            for (var pattern in this.opts.rules) {
                var route = this.opts.rules[pattern];
                this._rules.unshift(this.createUrlRule(route, pattern));
            }
        }
    };

    urlManager.prototype.createUrl = function (route, params, ampersand) {
        params = params || [];
        ampersand = ampersand || '&';

        params = JSON.parse(JSON.stringify(params));


        delete params[this.opts.routeVar];

        for (var i in params) {
            var param = params[i];
            if (param === null) {
                params[i] = '';
            }
        }

        var anchor = '';
        if ("#" in params) {
            anchor = '#' + params['#'];
            delete  params['#'];
        }

        route = route.replace(/\/+$/, "");
        for (var i in this._rules) {
            var rule = this._rules[i];
            var url = rule.createUrl(this, route, params, ampersand);

            if (url !== false) {
                if (rule.hasHostInfo) {
                    return url === '' ? '/' + anchor : url + anchor;
                } else {
                    return this.getBaseUrl() + '/' + url + anchor;
                }
            }
        }

        return this.createUrlDefault(route, params, ampersand) + anchor;
    };

    urlManager.prototype.createUrlDefault = function (route, params, ampersand) {
        if (this.opts.urlFormat === PATH_FORMAT) {
            var url = this.getBaseUrl() + '/' + route + '/';
            url = url.replace(/\/+$/, "");

            if (this.opts.appendParams) {
                var fullUrl = url + '/' + this.createPathInfo(params, '/', '/');
                url = fullUrl.replace(/\/+$/, "");
                return route === '' ? url : url + this.opts.urlSuffix;
            } else {
                if (route !== '') {
                    url += this.opts.urlSuffix;
                }

                var query = this.createPathInfo(params, '=', ampersand);
                return query === '' ? url : url + '?' + query;
            }
        } else {
            var url = this.getBaseUrl();

            var query = this.createPathInfo(params, '=', ampersand);
            if (!this.opts.showScriptName) {
                url += '/';
            }

            if (route !== '') {
                url += '?' + this.opts.routeVar + '=' + route;
                if (query !== '') {
                    url += ampersand + query;
                }
            }
            else if (query !== '') {
                url += '?' + query;
            }

            return url;
        }
    };

    urlManager.prototype.createPathInfo = function (params, equal, ampersand, key) {
        key = key || null;

        var pairs = [];
        for (var k in params) {
            var v = params[k];

            if (key !== null) {
                k = key + '[' + k + ']';
            }

            if (Object.prototype.toString.call(v) === '[object Array]') {
                pairs.push(this.createPathInfo(v, equal, ampersand, k));
            } else {
                pairs.push(encodeURIComponent(k) + equal + encodeURIComponent(v));
            }
        }

        return pairs.join(ampersand);
    };

    urlManager.prototype.createUrlRule = function (route, pattern) {
        return new UrlRule(route, pattern);
    };

    urlManager.prototype.getBaseUrl = function () {
        return (this.opts.showScriptName) ? Yii.app.scriptUrl : Yii.app.baseUrl;
    }

    return urlManager;
}).call(this);

var UrlRule = (function () {

    var urlRule = function (route, pattern) {
        this.references = {};
        this.params = {};
        this.caseSensitive = null;
        this.routePattern = null;
        this.defaultParams = {};
        this.matchValue = null;
        this.urlSuffix = null;
        this.matchValue = null;
        this.verb = null;
        this.routePatternGroups = [];


        if (typeof route === 'object') {
            var nameList = ['urlSuffix', 'caseSensitive', 'defaultParams', 'matchValue', 'verb', 'parsingOnly'];
            for (var key in nameList) {
                var name = nameList[key];
                if (name in route) {
                    this[name] = route[name];
                }
            }

            if ('pattern' in route) {
                pattern = route['pattern'];
            }

            route = route[0];
        }

        var tr2 = {};

        tr2['/'] = '\\/';
        this.route = route.replace(/\/+$/, "");


        if (route.indexOf('<') !== -1) {
            var referenceMatches = /<(\w+)>/g;
            var matches2 = referenceMatches.exec(route);
            while (matches2 !== null) {
                var name = matches2[1];

                this.references[name] = "<" + name + ">";
                matches2 = referenceMatches.exec(route);
            }
        }

        this.hasHostInfo = pattern.substring(0, 7) === 'http://' || pattern.substring(0, 8) === 'https://';

        //Verb ingnored for url creation

        var patternRegex = /<(\w+):?(.*?)?>/g;
        var matches = patternRegex.exec(pattern);
        var routePatternNo = 0;
        while (matches !== null) {
            var name = matches[1];
            var value = matches[2];

            if (value === '' || value === void 0) {
                value = '[^\/]+';
            }

            if (name in this.references) {
                tr2["<" + name + ">"] = "(?P<" + name + ">" + value + ")";
            } else {
                this.params[name] = value;
            }

            matches = patternRegex.exec(pattern);
        }

        var p = pattern.replace(/\*+$/, '');
        this.append = (p !== pattern);

        p = p.replace(/^\/|\/$/g, "");
        this.template = p.replace(/<(\w+):?.*?>/g, '<$1>');
        //Patern only used for parsing

        if (this.references !== {}) {
            this.routePattern = '^' + phpJS.strtr(this.route, tr2) + '$';
            var namedGroupPattern = /\(\?P<(\w+)>.*?\)/g;
            var namedGroups = namedGroupPattern.exec(this.routePattern);
            var groupNo = 0;
            while (namedGroups != null) {
                this.routePatternGroups[namedGroups[1]] = groupNo;
                groupNo++;
                namedGroups = namedGroupPattern.exec(this.routePattern);
            }

            this.routePattern = this.routePattern.replace(/\(\?P<\w+>(.*?)\)/g, "($1)");

            //Rewrite regex to make quantifiers non greedy
            this.routePattern = this.routePattern.replace(/([^\\])([\+\*])/g, "$1$2?");

            //Remove any double brackets to prevent double grouping
            this.routePattern = this.routePattern.replace(/\(\((.*?)\)\)/g, "($1)");
        }
    }

    urlRule.prototype.createUrl = function (manager, route, params, ampersand) {
        var caseSensitive = '';
        if (manager.opts.caseSensitive && this.caseSensitive === null || this.caseSensitive) {
            caseSensitive = 'i';
        }

        var tr = {};
        if (route !== this.route) {
            if (this.routePattern !== null) {
                var patternMatch = new RegExp(this.routePattern, caseSensitive);

                var matches = route.match(patternMatch);
                if (matches !== null) {
                    for (var key in this.references) {
                        var name = this.references[key];
                        var valueIndex = this.routePatternGroups[key];
                        tr[name] = matches[valueIndex + 1];
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        for (var key in this.defaultParams) {
            var value = this.defaultParams[key];
            if (key in params) {
                if (params[key] == value) {
                    delete params[key];
                } else {
                    return false;
                }
            }
        }

        for (var key in this.params) {
            if (!(key in params)) {
                return false;
            }
        }

        if (manager.opts.matchValue && this.matchValue === null || this.matchValue) {
            for (var key in this.params) {
                var value = this.params[key];

                var localParam = "" + params[key];
                if (localParam.match(new RegExp("^" + value + '$', caseSensitive)) === null) {
                    return false;
                }
            }
        }

        for (var key in this.params) {
            tr["<" + key + ">"] = encodeURIComponent(params[key]);
            delete params[key];
        }

        var suffix = this.urlSuffix === null ? manager.opts.urlSuffix : this.urlSuffix;
        var url = phpJS.strtr(this.template, tr);

        if (this.hasHostInfo) {
            var hostInfo = Yii.app.hostInfo.toLowerCase();
            if (url.toLowerCase().indexOf(hostInfo) === 0) {
                url = url.substring(hostInfo.length);
            }
        }
        if (Object.size(params) === 0) {
            return (url !== '') ? url + suffix : url;
        }

        if (this.append) {
            url += '/' + manager.createPathInfo(params, '/', '/') + suffix;
        }
        else {
            if (url !== '') {
                url += suffix;
            }
            url += '?' + manager.createPathInfo(params, '=', ampersand);
        }

        return url;
    }

    return urlRule;
}).call(this);