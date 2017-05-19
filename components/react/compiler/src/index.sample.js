// var uiReady = function(f) {
//     if (!window.ui) {
//         setTimeout(function() {
//             uiReady(f);
//         }, 100);
//     }
//     else {
//         f();
//     }
// }

uiReady(function() {
    window
        .ui
        .use([
            ['Zan', 'S.collections.Grid'],
            'S.elements.Icon'
        ])
        .render(function(h) {
                return h('div', [
                            h('Zan', {celled: true}, [
                                h('Zan.Row', [
                                    h('Zan.Column', {
                                        width: 3,
                                        onClick: (a,b,c) => {
                                            console.log(this, a, b, c);
                                        }
                                    }, [
                                        h('Icon', {
                                            name: 'wait'
                                        }),
                                        h('span', 'Yoooo Div')
                                    ]),
                                    h('Grid.Column', {width: 3}, [
                                        h('Icon', {
                                            name: 'wait'
                                        }),
                                        h('span', 'Yoooo Div')
                                    ])
                                ])
                            ])
                        ]);
        });
});

function pageReady(f) {
    if (!window.Page) {
        setTimeout(function() {
            pageReady(f);
        }, 100)
    }
    else f();
}

pageReady(function() {
    window.Page.Load
});