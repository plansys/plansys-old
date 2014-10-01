
var timeout = 5000; // WAIT 5 SECOND AND THE RE-QUERY NFY
var sys = require('sys');
var fs = require('fs');
var path = require('path');
var util = require("util");
var exec = require('child_process').exec;
var serverEvent = require('server-event');
var express; 
var app = require('express')();

sys.puts("\
Welcome to Plansys Nfy Server\n\
==============================\n\
\nListening to port 8981:\n");


app.listen(8981);
serverEvent = serverEvent({ express : app }); 

app.get('/:id', serverEvent, function (req, res) {
    var id = req.url.substr(1);
    if (id != '') {
        setInterval(function () {
            constructSSE(res, id);
        }, timeout);

        constructSSE(res, id);
    }
});
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
                var msg = JSON.stringify(dt[i]) ;
                res.sse(msg);
                console.log(msg);
            }
        }
    });
}
