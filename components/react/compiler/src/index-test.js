var uiReady = function(f) {
     if (!window.ui) {
          setTimeout(function() {
               uiReady(f);
          }, 100);
     }
     else {
          f();
     }
}

uiReady(function() {
     setTimeout(function() {
          window.ui.render(function(h) {
               return h('pdiv', [
                    'wadadsa'
               ]);
          });
     }, 500);

     setTimeout(function() {
          window.ui.render(function(h) {
               return h('div', [
                    'wadadsa',
                    h('Hello', 'World'),
                    h('Hello_Jon', 'Tenis'),
                    h('div', [
                         h('Rusak', 'Tenis'),
                    ])
               ]);
          });
     }, 2500);
});