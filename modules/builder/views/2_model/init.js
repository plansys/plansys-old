/* global builder, Yii */

builder.register('model', {
    col1: {
        url: Yii.app.createUrl('/builder/model/tree'),
    },
    col2: {
        url: Yii.app.createUrl('/builder/model/editor'),
    },
    $meta: {
        title: "Model",
        icon: "fa fa-cube",
        columns: ['col1', 'col2']
    }
});


