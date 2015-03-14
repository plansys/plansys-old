editor.ColumnField = {
    calculateWidth: function () {
        var w = (100 / editor.$scope.active.totalColumns).toFixed(0);
        for (var i = 1; i <= editor.$scope.active.totalColumns; i++) {
            editor.$scope.active['w' + i] = w + '%';
        }
    },
    changeTC: function () {
        this.calculateWidth();
        editor.$scope.save();
    },
    onSelect: function (item) {
        if (!item.w1) {
            this.calculateWidth();
            $timeout(function () {
                editor.$scope.save();
            });
        }
    }
};