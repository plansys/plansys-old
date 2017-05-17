import React from 'react';
import ReactDOM from 'react-dom';
import {
    AppContainer
}
from 'react-hot-loader';
import h from 'react-hyperscript';
import Page from './Page';

window.ui = {
    list: window.UIELEMENTS,
    loading: {},
    loaded: {},
    content: null,
    render: (renderer) => {
        const ui = window.ui;
        
        // store renderer function for future use
        if (renderer) ui.renderer = renderer; 

        // scan all rendered content for unloaded components(tag)
        const loader = (tag, props, children) => {
            if (ui.list.indexOf(tag) >= 0) {
                if (!ui.loaded[tag]) {
                    if (!ui.loading[tag]) {
                        ui.loading[tag] = () => importer(tag);
                    }
                }
            }
        }
        ui.renderer(loader); // execute scan
        
        // helper function to execute the actual import component 
        const importer = (tag) => {
            var etag = tag.replace(/_/g, '/');
            return import (/* webpackChunkName: tag */ `./ui/${etag}/index.js`)
                .then((res) => {
                    return res.default
                })
        }
        
        // swap loaded tag with react component
        const hswap = (tag, props, children) => {
            if (ui.list.indexOf(tag) >= 0) {
                if (ui.loaded[tag]) {
                    tag = ui.loaded[tag]
                }
            }
            
            return h(tag, props, children);
        }

        const tags = Object.keys(ui.loading);
        // if there are component need to be loaded
        if (tags.length > 0) {
            
            // warning: this is async!
            Promise
                // import all dependencies
                .all(tags.map((tag) => ui.loading[tag](tag))) 
                
                .then((result) => {
                    // move it to ui.loaded
                    tags.map((tag, idx) => {
                        ui.loaded[tag] = result[idx];
                        delete ui.loading[tag];
                    })
                    
                    // get renderer content with newly loaded comps
                    ui.content = ui.renderer(hswap);
                    
                    // tell react to render our component
                    render();
                })
                
        } else {
            // ok, all needed component is already loaded
            ui.content = ui.renderer(hswap);
            
            // tell react to render our component
            render();
        }
        
        
    },
    renderer: function() {
        return h('div', 'Loading components...');
    }
}

const render = () => {
    console.log('rendered');
    window.ui.reactdom = ReactDOM.render(
        <AppContainer>
            <Page content={ window.ui.content } />
        </AppContainer>,
        document.getElementById('root')
    );
};


window.ui.render();

if (module.hot) {
    module.hot.accept('./Page', () => {
        window.ui.render();
    });
}