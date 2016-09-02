/* 
 * Plansys Builder Object
 * Following is the hierarchy:
 * 
 *                   +-> column (yii ctrl)
 *         +--> view |
 *         |         +-> column (yii ctrl)
 * builder |
 *         |         +-> column (yii ctrl)
 *         +--> view |
 *                   +-> column (yii ctrl)
 *                   |
 *                   +-> ...
 */

var builder = {
    views: {}, // store builder's various views type
    ng: {}, // used to store angular services
    active: {}, // used to store builder active view
    register: function (viewName, view) { // register a view
        this.views[viewName] = view;
    },
    activate: function (viewName) { // activate a view
        if (!this.views[viewName]) { // bail out when viewName is not declared
            return false;
        }
        var view = this.active = this.views[viewName];

        // bind columns to activeCol
        view.$meta.columns.forEach(function (col, i) {
            this.ng.indexScope['col' + (i + 1)].view = view[col];
        }.bind(this));

        // initialize each col
        view.$meta.columns.forEach(function (col, i) {
            if (typeof view[col].init === 'function') {
                view[col].init();
            }
            if (!view[col].activated) {
                view[col].loading = true; // start loading when column is initializing
            }
        }.bind(this));

    },
    activated: function (colNum, e) { // column has loaded
        var col = this.ng.indexScope['col' + colNum].view;
        if (typeof col.loaded === "function") {
            col.loaded();
        }
        col.activated = true;
        col.loading = false; // stop loading when column is loaded
    }
};