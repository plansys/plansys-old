
var timeout = 5000; // WAIT 5 SECOND AND THE RE-QUERY NFY
var http = require('http');
var sys = require('sys');
var fs = require('fs');
var path = require('path');
var util = require("util");
var exec = require('child_process').exec;


sys.puts("\
Welcome to Plansys Nfy Server\n\
==============================\n\
\nListening to port 8981:\n");

http.createServer(function (req, res) {
    res.writeHead(200, {
        'Content-Type': 'text/event-stream',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Cache-Control': 'no-cache',
        'Connection': 'keep-alive'
    });

    var id = req.url.substr(1);

    if (id != '') {
        setInterval(function () {
            constructSSE(res, id);
        }, timeout);

        constructSSE(res, id);
    }
}).listen(8981);

function constructSSE(res, id) {
    var isWin = /^win/.test(process.platform);
    var command = 'php ' + process.cwd() + path.sep + 'plansys' + path.sep + 'yiic.php nfy receive --id=' + id;

    exec(command, function (err, data, code) {
        if (err instanceof Error)
            throw err;

        data = data.trim();

        if (data != '') {
            var dt = JSON.parse(data);
            var j = 1;
            for (i in dt) {
                var msg = "id: " + (new Date().getTime()) + "_" + i + "\r\ndata: " + JSON.stringify(dt[i]) + "\r\n\r\n";
                console.log(msg);
                res.write(msg);
            }

        }
    });
}
