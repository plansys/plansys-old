/* global builder, Yii */

builder.register('model', {
    tree: {
        url: Yii.app.createUrl('/builder/model/tree'),
    },
    editor: {
        url: Yii.app.createUrl('/builder/model/editor'),
    },
    $meta: {
        columns: ['tree', 'editor']
    }
});


