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