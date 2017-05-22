const path = require('path');
const fs = require("fs");
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const production = process.env.NODE_ENV == 'production';
const rimraf = require('rimraf');
const copy = require('copy');

var host = 'p.plansys.co';
var cwdir = __dirname.split(path.sep);
var uidir = cwdir.splice(0, cwdir.length - 1);
if (production) {
    uidir = uidir.join(path.sep) + path.sep + 'ui';
    var dlldir = path.resolve(__dirname, 'dll');
    rimraf(uidir, function() {
        copy(dlldir + path.sep + 'dll.prod.*', uidir, function() {
        });
        
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

        'webpack-dev-server/client?http://' + host + ':8080',
        // bundle the client for webpack-dev-server and connect to the provided endpoint

        'webpack/hot/only-dev-server',
        // bundle the client for hot reloading only- means to only hot reload for
        // successful updates

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
    publicPath: 'http://' + host + ':8080/'
};

if (production) {
    output.filename = '[name]_[chunkHash:5].min.js';
    output.chunkFilename = '[name]_[chunkHash:5].min.js';
    output.publicPath = '';
}
else {
    output.pathinfo = true;
}

/************************* GET UI ELEMENT LIST ****************************/
function listDir(dir, fn) {
    function flatten(lists) {
        return lists.reduce(function(a, b) {
            return a.concat(b);
        }, []);
    }

    function getDirectories(srcpath) {
        return fs
            .readdirSync(srcpath)
            .map(file => path.join(srcpath, file))
            .filter(path => fs.statSync(path).isDirectory());
    }

    function getDirectoriesRecursive(srcpath) {
        return [
            srcpath, ...flatten(getDirectories(srcpath).map(getDirectoriesRecursive))
        ];
    }
    return fn(getDirectoriesRecursive(dir));
}

const uieldir = path.resolve(__dirname, 'src', 'ui');
const uielements = [];
const uialias = {};
listDir(uieldir, (res) => {
    return res.map((d) => {
        var el = d.substr(uieldir.length + 1);
        if (el.indexOf('_') >= 0 || el.indexOf('.') >= 0) {
            var ed = el.replace(/[\_\.]/g, '');
            fs.renameSync(uieldir + path.sep + el, uieldir + path.sep + ed);
            el = ed;
        }
        if (!!el) {
            if (path.sep == '\\') {
                el = (el).replace(/\\/g, '.');
            }
            else {
                el = (el).replace(/\//g, '.');
            }
            
            uielements.push(el);
            uialias[el.split(".").pop()] = el;
        }
    })
});

/************************* DEFINE PLUGIN ****************************/

const plugins = [];
plugins.push(new webpack.DefinePlugin({
    'PRODUCTION': production,
    'window.UIELEMENTS': JSON.stringify(uielements),
    'window.UIALIAS': JSON.stringify(uialias)
}));
if (production) {
    plugins.push(new HtmlWebpackPlugin({
        inject: true,
        filename: 'index.html',
        template: 'index.prod.html'
    }))
    plugins.push(new webpack.optimize.UglifyJsPlugin());
    plugins.push(new webpack.DllReferencePlugin({
        context: path.join(__dirname, "client"),
        manifest: require("./dll/vendor-prod-manifest.json")
    }));
    plugins.push(new webpack.DllReferencePlugin({
        context: path.join(__dirname, "client"),
        manifest: require("./dll/lodash-prod-manifest.json")
    }));
}
else {
    plugins.push(new HtmlWebpackPlugin({
        inject: true,
        filename: 'index.html',
        template: 'index.dev.html'
    }))
    plugins.push(new webpack.HotModuleReplacementPlugin());
    plugins.push(new webpack.DllReferencePlugin({
        context: path.join(__dirname, "client"),
        manifest: require("./dll/vendor-dev-manifest.json")
    }));
}
/************************** DEV SERVER *****************************/
var devServer,
    devtool

if (!production) {
    devServer = {
        host: host,
        hot: true,
        compress:true,
        publicPath: '/',
        headers: {
            "Access-Control-Allow-Origin": "*"
        }
    }
    devtool = 'eval'
}

/************************** FINALIZE CONFIG *************************/
module.exports = {
    cache: true,
    context: path.resolve(__dirname, 'src'),
    entry,
    output,
    module: {
        loaders: [{
            test: /\.js$/,
            loader: 'babel-loader',
            exclude: /node_modules/
        }, {
            test: /\.css$/,
            use: ['style-loader', 'css-loader?modules']
        }]
    },
    plugins,
    devtool,
    resolveLoader: {
        modules: ["node_modules"],
        extensions: [
            ".js", ".json"
        ],
        mainFields: ["loader", "main"]
    },
    devServer
};