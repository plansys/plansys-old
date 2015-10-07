editor.ListView = {
    toggleEdit: function (active) {
        active.edited = !editor.$scope.active.edited;
        editor.$timeout(function () {
            $(".list-view-single-edit [ng-model='active.name']").hide();
        });
    },
    fieldTypeChange: function (active) {
        if (active.singleViewOption == null) {
            switch (active.singleView) {
                case "TextField":
                    active.singleViewOption = {
                        name: 'val',
                        fieldType: 'text',
                        labelWidth: 0,
                        fieldWidth: 12,
                        fieldOptions: {
                            'ng-delay': 500
                        }
                    };
                    break;
            }
            editor.$scope.save();
        }
        editor.$timeout(function () {
            $(".list-view-single-edit [ng-model='active.name']").hide();
        });
    },
    onLoad: function (active) {
        this.fieldTypeChange(active);
    }
}
