app.component('treeView', {
    templateUrl: Yii.app.createUrl('formfield/TreeView.template'),  
    controller: function () {
        this.$onInit = function() {
            console.log("HELO");
        }
    }
});