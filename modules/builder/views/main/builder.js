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
 *                   
 *                   
 * To add new view, you just copy one of the available view folder, and then 
 * rename it, and edit init.js , plansys will load your init.js automatically.
 *                   
 */



var builder = {
    views: {}, // store builder's various views type
    register: function (viewName, view) { // register a view
        view.name = viewName;
        this.views[viewName] = view;
    },
};