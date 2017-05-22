var path = require("path");
var webpack = require("webpack");
var rimraf = require("rimraf");

var cwdir = __dirname.split(path.sep);
var uidir = cwdir.join(path.sep) + path.sep + 'dll';
rimraf(uidir, function() {
    console.log('Deleted: ' + uidir);
    console.log('Executing webpack...');
});

module.exports = {
    entry: {
        vendor: [path.join(__dirname, "vendors.all.js")]
    },
    output: {
        path: path.join(__dirname, "dll"),
        filename: "dll.dev.[name].js",
        library: "[name]"
    },
    plugins: [
        new webpack.DllPlugin({
            path: path.join(__dirname, "dll", "[name]-dev-manifest.json"),
            name: "[name]",
            context: path.resolve(__dirname, "client")
        })
    ],
    resolve: {
        modules: ["node_modules"]
    }
};