var builder = {
    views: {}, // store builder's various views type
    register: function (viewName, view) { // register a view
        view.name = viewName;
        this.views[viewName] = view;
    },
};