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
               return h('p > div', [
                    'wadadsa'
               ]);
          });
     }, 500);

     setTimeout(function() {
          console.log(window.ui);
          window.ui.render(function(h) {
               return h('div', [
                    'wadadsa',
                    h('Hello', 'World'),
                    h('Rusak', 'Tenis'),
                    h('div', [
                         h('Rusak', 'Tenis'),
                    ])
               ]);
          });
     }, 2500);
});