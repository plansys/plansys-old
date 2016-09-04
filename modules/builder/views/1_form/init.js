/* global builder, Yii */

builder.register('form', {
    col1: {
        url: Yii.app.createUrl('/builder/form/tree'),
    },
    col2: {
        url: Yii.app.createUrl('/builder/form/editor'),
    },
    col3: {
        url: Yii.app.createUrl('/builder/form/properties'),
    },
    $meta: {
        title: "Form",
        icon: "fa fa-file-text-o",
        columns: ['col1', 'col2', 'col3']
    }
});


