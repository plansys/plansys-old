app.directive('psDataTable', function ($timeout, $http, $compile, dateFilter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];

                var colHeaders = [];
                var columns = [];
                for (i in $scope.columns) {
                    var c = $scope.columns[i];
                    colHeaders.push(c.label);
                    columns.push({
                        data: c.name
                    });
                }

                // fixedHeader
                if ($scope.gridOptions['fixedHeader'] !== false) {
                    $timeout(function () {
                        var $container = $el.parents('.container-full');
                        var $dgcontainer = $el.find(".data-table-container");
                        var $topp = $el.find('.data-table-container .ht_clone_top');
                        var $form = $el.parents("form");
                        var formTopPos = Math.abs($form.position().top - $form.offset().top);
                        var formTop = $form.offset().top;
                        var top = formTop;


                        function fixHead() {
                            if (($container.scrollTop() > top) || $scope.gridOptions['fixedHeader'] == "always") {
                                if (!$dgcontainer.hasClass('fixed')) {
                                    $dgcontainer.addClass('fixed');
                                }
                                $topp
                                        .css('top', top)
                                        .css('left', $dgcontainer.offset().left)
                                        .height(50);
                            } else {
                                if ($dgcontainer.hasClass('fixed')) {
                                    $dgcontainer.removeClass('fixed');
                                }
                            }
                        }

                        $(window).resize(fixHead);
                        $el.on('mouseover', 'td', fixHead);
                        $el.on('mousedown', 'td', fixHead);
                        $container.scroll(fixHead);
                        fixHead();
                    }, 0);
                }

                var options = $.extend({
                    data: $scope.datasource.data,
                    minSpareRows: 1,
                    columnSorting: true,
                    contextMenu: true,
                    colHeaders: colHeaders,
                    columns: columns,
                    contextMenu: ['row_above', 'row_below', '---------', 'remove_row', '---------', 'undo', 'redo']
                }, $scope.gridOptions);

                $("#" + $scope.renderID).handsontable(options);
            }
        }
    }
});