import React from 'react';
import ReactDOM from 'react-dom';
import {
    AppContainer
}
from 'react-hot-loader';
import Page from './Page';

window.Page = Page;

const render = () => {
    ReactDOM.render(
        <AppContainer>
        <Page />
    </AppContainer>, document.getElementById('root'));
};

render();

if (module.hot) {
    module
        .hot
        .accept('./Page', () => {
            render();
        });
}