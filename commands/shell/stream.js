var sse = require('connect-sse')();
var express = require('express');
var path = require('path');
var mysql = require('mysql');
var rekuire = require('rekuire');
var dateFormat = require('dateformat');
var nodemailer = require('nodemailer');
var smtpPool = require('nodemailer-smtp-pool');
var validator = require('validator');
var htmlToText = require('html-to-text');
var swig = require('swig');
var app = express();
var config = rekuire('../../config/settings.json');
var pool = mysql.createPool({
    connectionLimit: 100,
    host: config.db.server,
    user: config.db.username,
    password: config.db.password
});
if (config.email && config.email.transport) {
    var transport = nodemailer.createTransport(smtpPool(config.email.transport));
    function getTemplate(name, tpl) {
        if (config.email && config.email.template && config.email.template[name]) {
            return config.email.template[name];
        } else {
            return tpl;
        }
    }

    var template = {
        notification: getTemplate('notification', 'app/static/email/notification.twig')
    };
    for (i in template) {
        template[i] = swig.compileFile(path.resolve(__dirname, "../../../" + template[i]));
    }
}

var resPool = {};

streamQuery = setInterval(function () {
    pool.getConnection(function (err, conn) {
        conn.query("USE " + config.db.dbname, function (err, rows) {
            // epic sql, do not edit!
            var sql = 'select u.email as to_email, u.fullname as to_name, a.* from \
	((SELECT t.*, "Notification" as sender_name, "SYSTEM" as sender_role FROM p_nfy_messages t \
        WHERE sender_id = 0 AND status = 0 AND subscription_id is not null)  \
	UNION \
	(SELECT t.*, p.fullname as sender_name, r.role_name as sender_role FROM p_nfy_messages t \
       inner join p_user p      on t.sender_id = p.id \
       inner join p_user_role q on q.user_id = p.id \
       inner join p_role r      on q.role_id = r.id \
        WHERE sender_id <> 0 AND status = 0 AND subscription_id is not null) \
    ) a \
        inner join p_nfy_subscriptions p on p.id = a.subscription_id \
        inner join p_user u on p.subscriber_id = u.id';

            conn.query(sql, function (err, rows) {
                if (typeof rows != "undefined" && rows.length > 0) {

                    var ids = [];
                    for (i in rows) {
                        ids.push(rows[i].id);
                        rows[i].created_on = dateFormat(rows[i].created_on, "yyyy-mm-dd HH:MM:ss")
                    }
                    var sql = "UPDATE p_nfy_messages set status = 1 WHERE id IN (" + ids.join(",") + ")";
                    conn.query(sql, function () {
                        // send notification messages
                        for (i in rows) {
                            var row = rows[i];
                            if (resPool[row.subscription_id]) {
                                resPool[row.subscription_id].json([row]);
                                console.log("Subscriber #" + row.subscription_id + " sent: " + JSON.stringify([row]));
                            }
                        }

                        // send e-mail notification
                        if (config.email && config.email.from) {
                            for (i in rows) {
                                var row = rows[i];
                                if (!validator.isEmail(row.to_email)) {
                                    continue;
                                }
                                var body = JSON.parse(row.body);
                                var url = config.app.host + body.url;
                                var subject = config.app.name ? config.app.name + " - " + body.message : body.message;
                                var mailOptions = {
                                    from: config.email.from, // sender address
                                    to: row.to_email, // list of receivers
                                    subject: subject, // Subject line,
                                    url: url
                                };

                                mailOptions.html = template.notification(mailOptions);
                                mailOptions.text = htmlToText.fromString(mailOptions.html, {
                                    wordwrap: 130
                                });

                                transport.sendMail(mailOptions, function (error, info) {
                                    if (error) {
                                        console.log(error);
                                    } else {
                                        console.log('Subscriber #' + row.subscription_id + ' mail sent:' + info.response + "\nE-mail Content:" + JSON.stringify(mailOptions));
                                    }
                                });
                            }
                            transport.close();
                        }

                        conn.release();
                    });
                } else {
                    conn.release();
                }
            });
        });
    });
}, 1000);

app.set('port', 8981);
app.use(express.static(path.join(__dirname, 'public')));
app.all('*', function (req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});
app.get('/:id', sse, function (req, res) {
    console.log("Subscriber #" + req.params.id + " connected");

    resPool[req.params.id] = res;

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