window.mode.image = {
     open: function(item) {
          item.loading = false;
          $("#image-mode-show").attr('src', item.d);
     }
}