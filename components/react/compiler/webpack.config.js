const path = require('path');
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const production = process.env.NODE_ENV == 'production';
const rimraf = require('rimraf');
const fs = require("fs");

var cwdir = __dirname.split(path.sep);
var uidir = cwdir.splice(0, cwdir.length - 1);
if (production) {
     uidir = uidir.join(path.sep) + path.sep + 'ui';
     rimraf(uidir, function() {
          console.log('Deleted: ' + uidir);
          console.log('Executing webpack...')
     });
}
else {
     uidir = path.resolve(__dirname, 'public')
}
/************************* DEIFNE ENTRY *****************************/
var entry;
if (!production) {
     entry = [
          'react-hot-loader/patch',
          // activate HMR for React

          'webpack-dev-server/client?http://p.plansys.co:8080',
          // bundle the client for webpack-dev-server
          // and connect to the provided endpoint

          'webpack/hot/only-dev-server',
          // bundle the client for hot reloading
          // only- means to only hot reload for successful updates

          './index.js'
          // the entry point of our app
     ]
}
else {
     entry = './index.js'
}
/************************* DEFINE OUTPUT ****************************/
const output = {
     filename: 'bundle.js',
     path: uidir,
     publicPath: 'http://p.plansys.co:8080/yourapp',
};

if (production) {
     output.filename = '[name]_[chunkHash:5].min.js';
     output.chunkFilename = '[name]_[chunkHash:5].min.js';
     output.publicPath = '';
}

/************************* GET UI ELEMENT LIST ****************************/

function listDir(dir, fn) {
     function flatten(lists) {
          return lists.reduce(function(a, b) {
               return a.concat(b);
          }, []);
     }

     function getDirectories(srcpath) {
          return fs.readdirSync(srcpath)
               .map(file => path.join(srcpath, file))
               .filter(path => fs.statSync(path).isDirectory());
     }

     function getDirectoriesRecursive(srcpath) {
          return [srcpath, ...flatten(getDirectories(srcpath).map(getDirectoriesRecursive))];
     }

     return fn(getDirectoriesRecursive(dir));
}
const uieldir = path.resolve(__dirname, 'src', 'ui');
const uielements = [];
listDir(uieldir, (res) => {
     return res.map((d) => {
          var el = d.substr(uieldir.length + 1);
          if (!!el) {
               var re = new RegExp(path.sep, "g");
               uielements.push((el).replace(re, '_'));
          }
     })
});


/************************* DEFINE PLUGIN ****************************/

const plugins = [];
plugins.push(
     new webpack.DefinePlugin({
          'PRODUCTION': production,
          'window.UIELEMENTS': JSON.stringify(uielements)
     })
);
plugins.push(
     new HtmlWebpackPlugin({
          inject: true,
          filename: 'index.html',
          template: 'index.html'
     })
)

if (production) {
     plugins.push(
          new webpack.optimize.UglifyJsPlugin()
     );
}
else {
     plugins.push(new webpack.HotModuleReplacementPlugin());
}
/************************** DEV SERVER *****************************/
var devServer, devtool
if (!production) {
     devServer = {
          host: 'p.plansys.co',
          hot: true,
          publicPath: '/'
     }
     devtool = 'inline-source-map'
}

/************************** FINALIZE CONFIG *************************/
module.exports = {
     context: path.resolve(__dirname, 'src'),
     entry,
     output,
     module: {
          loaders: [{
               test: /\.js$/,
               loader: 'babel-loader',
               exclude: /node_modules/,
          }, {
               test: /\.css$/,
               use: ['style-loader', 'css-loader?modules', ],
          }]
     },
     plugins,
     devtool,
     resolveLoader: {
          modules: ["node_modules"],
          extensions: [".js", ".json"],
          mainFields: ["loader", "main"]
     },
     devServer
};