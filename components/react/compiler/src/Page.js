import React from 'react';
import PropTypes from 'prop-types';
import h from 'react-hyperscript';

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

Page.propTypes = {
     content: PropTypes.element
}

export default Page;