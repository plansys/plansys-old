function ngGridFlexibleHeightPlugin(opts) {
    var self = this;
    self.grid = null;
    self.scope = null;
    self.init = function(scope, grid, services) {
        self.domUtilityService = services.DomUtilityService;
        self.grid = grid;
        self.scope = scope;
        var recalcHeightForData = function() {
            setTimeout(innerRecalcForData, 1);
        };
        var innerRecalcForData = function() {
            var gridId = self.grid.gridId;
            var footerPanelSel = '.' + gridId + ' .ngFooterPanel';
            var extraHeight = self.grid.$topPanel.height() + $(footerPanelSel).height();
            var naturalHeight = self.grid.$canvas.height() + 1;
            if (opts != null) {
                if (opts.minHeight != null && (naturalHeight + extraHeight) < opts.minHeight) {
                    naturalHeight = opts.minHeight - extraHeight - 2;
                }
                if (opts.maxHeight != null && (naturalHeight + extraHeight) > opts.maxHeight) {
                    naturalHeight = opts.maxHeight;
                }
            }

            var newViewportHeight = naturalHeight + 23;
            if (!self.scope.baseViewportHeight || self.scope.baseViewportHeight !== newViewportHeight) {
                self.grid.$viewport.css('height', newViewportHeight + 'px');
                self.grid.$root.css('height', (newViewportHeight + extraHeight) + 'px');
                self.scope.baseViewportHeight = newViewportHeight;
                self.domUtilityService.RebuildGrid(self.scope, self.grid);
            }
        };
        self.scope.catHashKeys = function() {
            var hash = '',
                    idx;
            for (idx in self.scope.renderedRows) {
                hash += self.scope.renderedRows[idx].$$hashKey;
            }
            return hash;
        };
        self.scope.$watch('catHashKeys()', innerRecalcForData);
        self.scope.$watch(self.grid.config.data, recalcHeightForData);
    };
}function anchorLastColumn () {
    var self = this;
    self.grid = null;
    self.scope = null;
    self.services = null;
    self.init = function (scope, grid, services) {
      self.grid = grid;
      self.scope = scope;
      self.services = services;

      self.scope.$watch('isColumnResizing', function (newValue, oldValue) {
        if (newValue === false && oldValue === true) { //on stop resizing
          var gridWidth = self.grid.rootDim.outerWidth;
          var viewportH = self.scope.viewportDimHeight();
          var maxHeight = self.grid.maxCanvasHt;
          if(maxHeight > viewportH) { // remove vertical scrollbar width
              gridWidth -= self.services.DomUtilityService.ScrollW;
          }

          var cols = self.scope.columns;
          var col = null, i = cols.length;
          while(col == null && i-- > 0) {
              if(cols[i].visible) {
                  col = cols[i]; // last column VISIBLE
              }
          }
          var sum = 0;
          for(var i = 0; i < cols.length - 1; i++) {
                if(cols[i].visible) {
                    sum += cols[i].width;
                }
          }

          if(sum + col.minWidth <= gridWidth) {
            col.width = gridWidth - sum; // the last gets the remaining
          }
        }
      });
    }
}

app.directive('categoryHeader', function() {
    function link(scope, element, attrs) {

        // create cols as soon as $gridscope is avavilable
        // grids in tabs with lazy loading come later, so we need to 
        // setup a watcher
        scope.$watch('categoryHeader.$gridScope', function(gridScope, oldVal) {
            if (!gridScope) {
                return;
            }
            // setup listener for scroll events to sync categories with table
            var viewPort = scope.categoryHeader.$gridScope.domAccessProvider.grid.$viewport[0];
            var headerContainer = scope.categoryHeader.$gridScope.domAccessProvider.grid.$headerContainer[0];

            // watch out, this line usually works, but not always, because under certains conditions
            // headerContainer.clientHeight is 0
            // unclear how to fix this. a workaround is to set a constant value that equals your row height 
            scope.headerRowHeight = 28;

            angular.element(viewPort).bind("scroll", function() {
                // copy total width to compensate scrollbar width
                $(element).find(".categoryHeaderScroller")
                        .width($(headerContainer).find(".ngHeaderScroller").width());
                $(element).find(".ngHeaderContainer")
                        .scrollLeft($(this).scrollLeft());
            });

            // setup listener for table changes to update categories                
            scope.categoryHeader.$gridScope.$on('ngGridEventColumns', function(event, reorderedColumns) {
                createCategories(event, reorderedColumns);
            });
        });
        var createCategories = function(event, cols) {
            scope.categories = [];
            var lastDisplayName = "";
            var totalWidth = 0;
            var left = 0;

            angular.forEach(cols, function(col, key) {
                if (!col.visible) {
                    return;
                }
                totalWidth += col.width;
                var displayName = (typeof (col.colDef.categoryDisplayName) === "undefined") ?
                        "\u00A0" : col.colDef.categoryDisplayName;
                if (displayName !== lastDisplayName) {
                    scope.categories.push({
                        displayName: lastDisplayName,
                        width: totalWidth - col.width,
                        left: left
                    });
                    left += (totalWidth - col.width);
                    totalWidth = col.width;
                    lastDisplayName = displayName;
                }
            });
            if (totalWidth > 0) {
                scope.categories.push({
                    displayName: lastDisplayName,
                    width: totalWidth,
                    left: left
                });
            }
        };
    }
    return {
        scope: {
            categoryHeader: '='
        },
        restrict: 'EA',
        templateUrl: 'category_header',
        link: link
    };
});
  