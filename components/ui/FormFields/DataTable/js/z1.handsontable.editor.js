
(function (Handsontable) {

    /*************** RELATION TYPE *******************/
    function relationRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.AutocompleteCell.renderer.apply(this, arguments);

        return td;
    }
    Handsontable.renderers.registerRenderer('relation', relationRenderer);

    var RelationEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();
    RelationEditor.prototype.open = function () {
        var opt = this.instance.getSettings().columns[this.instance.getActiveEditor().col];
        opt.scope.triggerWatch = false;
        opt.originalValue = this.originalValue;

        Handsontable.editors.AutocompleteEditor.prototype.open.apply(this, arguments);
    }

    RelationEditor.prototype.close = function () {
        var ins = this.instance;
        var s = ins.getActiveEditor();
        var col = s.col;
        var row = s.row;
        var opt = this.instance.getSettings().columns[col];

        setTimeout(function () {
            var label = ''
            if (opt.scope.datasource.data[row]) {
                label = opt.scope.datasource.data[row][opt.name + "_label"];
            } else {
                return false;
            }
            var value = opt.scope.lastRelList[label];
            if (typeof value != "undefined") {
                opt.scope.datasource.data[row][opt.name] = value;
            } else {
                ins.setDataAtCell(row, col, opt.originalValue);
            }
            setTimeout(function () {
                opt.scope.triggerWatch = true;
            }, 0);
        }, 0);
        Handsontable.editors.AutocompleteEditor.prototype.close.apply(this, arguments);

    }
    Handsontable.editors.RelationEditor = RelationEditor;
    Handsontable.editors.registerEditor('relation', RelationEditor);

    /*************** DATE TIME / INPUT MASK *******************/
    function formatDate(val, format, $filter, td) {
        var oldval = val;
        if (typeof oldval != "string")
            oldval = "";

        if (typeof val == "string") {
            var t = val.split(/[- :]/);
            if (t.length > 3) {
                val = new Date(t[0], t[1] - 1, t[2], t[3], t[4]);
            } else if (t.length == 3) {
                if (val.indexOf(':') > 0) {
                    val = new Date(1, 0, 0, t[0], t[1], t[2]);
                } else {
                    val = new Date(t[0], t[1] - 1, t[2], 0, 0);
                }
            } else if (t.length == 2) {
                val = new Date();
                val.setMinutes(t[0]);
                val.setMinutes(t[1]);
            }
            console.log(val);

            if (val == "Invalid Date" || (typeof val == 'object' && val.getFullYear() < 1900)) {
                val = "";
            }
        } else {
            val = "";
        }

        switch (format) {
            case "99/99/9999":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy'));
                } else {
                    if (oldval.split(/\//).length == 3)
                        return oldval;

                    if (td) {
                        val = "dd/mm/yyyy";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99/99/9999 99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy HH:mm'));
                } else {

                    if (td) {
                        val = "dd/mm/yyyy hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'HH:mm'));
                } else {

                    if (td) {
                        val = "hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
        }
        return val;
    }

    function dateTimeRenderer(instance, td, row, col, prop, value, cellProperties) {
        var options = instance.getSettings().columns[col];
        var val = value;
        var oldval = value;

        val = formatDate(val, options.inputMask, options.filter, td);

        $(td).html(val);

        return td;
    }
    Handsontable.renderers.registerRenderer('datetime', dateTimeRenderer);

    var InputMaskEditor = Handsontable.editors.TextEditor.prototype.extend();
    InputMaskEditor.prototype.createElements = function () {
        Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

        var row = this.instance.getSelected()[0];
        var col = this.instance.getSelected()[1];
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        this.TEXTAREA = document.createElement('input');
        this.TEXTAREA.setAttribute('type', 'text');
        this.TEXTAREA.className = 'handsontableInput';
        this.textareaStyle = this.TEXTAREA.style;
        this.textareaStyle.width = 0;
        this.textareaStyle.height = 0;
        this.$textarea = $(this.TEXTAREA);

        Handsontable.Dom.empty(this.TEXTAREA_PARENT);
        this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

        $(this.TEXTAREA)
                .bind('focus', function () {
                    var val = $(this).val();
                    $(this).val(formatDate(val, options.inputMask, options.filter));
                    $(this).mask(options.inputMask);
                })

    };

    InputMaskEditor.prototype.close = function () {
        var ed = this.instance.getActiveEditor();
        var row = ed.row;
        var col = ed.col;
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        var val = $(this.TEXTAREA).val();
        var $filter = options.filter;

        Handsontable.editors.TextEditor.prototype.close.apply(this, arguments);

        switch (options.inputMask) {
            case "99/99/9999":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99/99/9999 99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0], t[3], t[4]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date();
                d.setHours(t[0]);
                d.setMinutes(t[1]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
        }

    };
    Handsontable.editors.InputMaskEditor = InputMaskEditor;
    Handsontable.editors.registerEditor('mask', InputMaskEditor);

})(Handsontable);
