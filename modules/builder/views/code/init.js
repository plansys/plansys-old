/* global builder, Yii */

builder.register('code', {
    tree: {
        url: Yii.app.createUrl('/builder/code/tree'),
        type: 'tree',
        data: [],
        expand: function() {
            
        },
        init: function() {
            
        },
        loaded: function() {
            
        }
    },
    editor: {
        url: Yii.app.createUrl('/builder/code/editor'),
    },
    properties: {
        url: Yii.app.createUrl('/builder/code/properties'),
    },
    $meta: {
        columns: ['tree', 'editor', 'properties']
    }
});


