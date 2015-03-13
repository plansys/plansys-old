editor.ColumnField = {
    changeTC: function () {
        var w = (100 / editor.$scope.active.totalColumns).toFixed(0);
        for (var i = 1; i <= editor.$scope.active.totalColumns; i++) {
            editor.$scope.active['w' + i] = w + '%';
        }
        editor.$scope.save();
    }
};