/* global builder, Yii */

builder.register('form', {
    tree: {
        url: Yii.app.createUrl('/builder/form/tree'),
    },
    editor: {
        url: Yii.app.createUrl('/builder/form/editor'),
    },
    properties: {
        url: Yii.app.createUrl('/builder/form/properties'),
    },
    $meta: {
        columns: ['tree', 'editor', 'properties']
    }
});


