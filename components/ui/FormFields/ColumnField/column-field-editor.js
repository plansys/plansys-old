editor.ColumnField = {
    calculateWidth: function (item) {
        var w = (100 / item.totalColumns).toFixed(0);
        for (var i = 1; i <= item.totalColumns; i++) {
            item['w' + i] = w + '%';
        }
    },
    changeTC: function () {
        this.calculateWidth(editor.$scope.active);
        editor.$scope.save(editor.$scope.active);
    },
    onSelect: function (item) {
        if (!item.w1) {
            this.calculateWidth(item);
            editor.$timeout(function () {
                editor.$scope.save();
            });
        }
    },
    onLoad: function (item) {
        this.onSelect(item);
        this.refreshColumnPlaceholder(item);
    },
    refreshColumnPlaceholder: function () {
        editor.$timeout(function () {
            $(".cpl").each(function () {
                if ($(this).parent().find("li").length == 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }
};