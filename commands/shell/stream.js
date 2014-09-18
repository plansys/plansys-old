var http = require('http');
var sys = require('sys');
var fs = require('fs');
var path = require('path');
var exec = require('child_process').exec;

http.createServer(function (req, res) {
    res.writeHead(200, {
        'Content-Type': 'text/event-stream',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Cache-Control': 'no-cache',
        'Connection': 'keep-alive'
    });

    var id = (new Date()).toLocaleTimeString();

    setInterval(function () {
        constructSSE(res, id);
    }, 5000);

    constructSSE(res, id);
}).listen(8981);

function constructSSE(res, id) {
    var isWin = /^win/.test(process.platform);
    var command = 'php ' + process.cwd().split(path.sep).slice(0, -2).join(path.sep) + path.sep + 'yiic.php nfy receive --id=1';

    exec(command, function (err, out, code) {
        if (err instanceof Error)
            throw err;

        data = out;

        sys.puts(data);
        res.write('id: ' + id + '\n');
        res.write("data: " + data + '\n\n');
    });

}
