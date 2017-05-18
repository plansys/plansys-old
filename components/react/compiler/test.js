const fs = require('fs');
const path = require('path');

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
console.log(getDirectoriesRecursive(path.resolve(__dirname, 'src','ui')))