//console.log(Yii.app.createUrl('formfield/treeView.template'));
app.component('treeView', {
    templateUrl: Yii.app.createUrl('formfield/treeView.template'),
    controller: function () {
        console.log(this);
    },
    bindings: {
        data: '='
    }
});