/* globals builder */

builder.register('form', {
    tree: {
        url: Yii.app.createUrl('/dev/builderForm/tree'),
    },
    editor: {
        url: Yii.app.createUrl('/dev/builderForm/editor'),
    },
    properties: {
        url: Yii.app.createUrl('/dev/builderForm/properties'),
    }
});


