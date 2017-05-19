import React from 'react';
import PropTypes from 'prop-types';
import h from 'react-hyperscript';

// helper function to execute the actual import component
const importer = (tag) => {
    tag = tag.replace(/\./g, '_');
    var etag = tag.replace(/\_/g, '/');
    var im =
        import (`./ui/${etag}/index.js`);
    return im.then((res) => {
        return res.default
    })
}

class Page extends React.Component {
     constructor() {
          super(...arguments)
          this.state = {
               content: h('div', 'Waiting for content...')
          };
     }

     componentWillMount() {
          if (React.isValidElement(this.props.content)) {
               this.setState({
                    content: this.props.content
               });
          } else {
               this.setState({
                    content: h('div', 'Invalid Content')
               });
          }
     }

     componentWillReceiveProps(nextProps) {
          if (React.isValidElement(nextProps.content)) {
               this.setState({
                    content: nextProps.content
               });
          }
     }

     render() {
          return this.state.content;
     }
}

export default Page;