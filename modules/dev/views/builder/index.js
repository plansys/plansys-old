var builder = {
    views: {}, // store builder's various views type
    ng: {}, // used to store angular services
    active: {}, // used to store builder active view
    register: function(viewName, view) { // register a view
        this.views[viewName] = view;
    },
    activate: function(viewName) { // activate a view
        if (!this.views[viewName]) {
            return false;
        }

        var view = this.views[viewName];
        this.active = view;
        
        // hide properties if not present
        var w = $("body").width() - $("#tree").width() - 1;
        if (!view.properties) {
            $("#properties").hide();
            $("#properties").prev().hide();
            $("#editor").width(w);
        } else {
            $("#properties").show();
            $("#properties").prev().show();
            $("#editor").width(w - ($("#properties").width() + 1));
        }

    }
}