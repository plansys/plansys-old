var sse = require('connect-sse')();
var express = require('express');
var path = require('path');
var mysql = require('mysql');
var rekuire = require('rekuire');
var dateFormat = require('dateformat');
var app = express();
var config = rekuire('../../config/settings.json');
var pool = mysql.createPool({
    connectionLimit: 100,
    host: config.db.server,
    user: config.db.username,
    password: config.db.password
});
app.set('port', 8981);
app.use(express.static(path.join(__dirname, 'public')));
app.all('*', function (req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});
app.get('/:id', sse, function (req, res) {
    console.log("Subscriber #" + req.params.id + " connected");
    res.streamQuery = setInterval(function () {
        pool.getConnection(function (err, conn) {
            conn.query("USE " + config.db.dbname, function (err, rows) {
                var sql = 'SELECT t.*, p.fullname as sender_name, r.role_name as sender_role FROM p_nfy_messages t \
                               inner join p_user p      on t.sender_id = p.id \
                               inner join p_user_role q on q.user_id = p.id \
                               inner join p_role r      on q.role_id = r.id \
                                WHERE status = 0 AND subscription_id = ' + req.params.id;

                conn.query(sql, function (err, rows) {
                    if (typeof rows != "undefined" && rows.length > 0) {

                        var ids = [];
                        for (i in rows) {
                            ids.push(rows[i].id);
                            rows[i].created_on = dateFormat(rows[i].created_on, "yyyy-mm-dd HH:MM:ss")
                        }
                        var sql = "UPDATE p_nfy_messages set status = 1 WHERE id IN (" + ids.join(",") + ")";
                        conn.query(sql, function () {
                            res.json(rows);
                            console.log("Subscriber #" + req.params.id + " sent: " + JSON.stringify(rows));
                            conn.release();
                        });
                    } else {
                        conn.release();
                    }
                });
            });
        });
    }, 1000);
    res.on("error", function () {
        clearInterval(res.streamQuery);
        console.log("Subscriber #" + req.params.id + " error");
    });
    res.on("close", function () {
        clearInterval(res.streamQuery);
        console.log("Subscriber #" + req.params.id + " disconnected");
    });
});
app.listen(app.get('port'));
console.log('\
----------------------------------------\n\
Welcome To Plansys Notification Server\n\
Nfy server listening on port ' + app.get('port') + "\n\
---------------------------------------\n\
");