const path = require('path');
const fs = require("fs");

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
listDir(uieldir, (res) => {
    return res.map((d) => {
        var el = d.substr(uieldir.length + 1);
        if (el.indexOf('_') >= 0 || el.indexOf('.') >= 0) {
            var ed = el.replace(/[\_\.]/g, '');
            fs.renameSync(uieldir + path.sep + el, uieldir + path.sep + ed);
            el = ed;
        }
        import(`./src/${el}`);
    })
});

