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