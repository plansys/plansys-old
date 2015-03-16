var process = require("process");
var rekuire = require('rekuire');
var fs = require('fs');
var nodemailer = require('nodemailer');
var smtpTransport = require('nodemailer-smtp-transport');
var config = rekuire(process.argv[2]+'/setting.json');

var transporter = nodemailer.createTransport(smtpTransport(config.email.transport));
var errorLog = process.argv[2]+'/error.log';
var flag = process.argv[2]+'/email.lock';
transporter.sendMail({
    from: config.email.from,
    to: 'tesMail@mailinator.com',
    subject: 'hello',
    text: 'hello world!'
},function(err, result){
    if(err){
        fs.writeFile(errorLog,err);
    }
    fs.unlink(flag);
});
