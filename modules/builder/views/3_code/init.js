/* global builder, Yii */

builder.register('code', {
    col1: {
        url: Yii.app.createUrl('/builder/code/tree'),
        type: 'tree',
        data: [],
        expand: function () {

        },
        init: function () {

        },
        loaded: function () {

        }
    },
    col2: {
        url: Yii.app.createUrl('/builder/code/editor'),
    },
    col3: {
        url: Yii.app.createUrl('/builder/code/properties'),
    },
    $meta: {
        title: "Code",
        icon: "fa fa-code",
        columns: ['col1', 'col2', 'col3']
    }
});


