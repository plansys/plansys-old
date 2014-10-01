server-event
============

Library to use Server Sent Events easy in node and on the client side with EventSource. Can be used as express middleware. Current browser support can be found here: http://caniuse.com/eventsource

Usage:
========

## Server:
	
As express middleware:

```javascript
var serverEvent, express, app;

serverEvent = require('server-event');
app = require('express')();

app.listen(8080);

serverEvent = serverEvent({ express : app }); // Optional. Pass in reference to express to access client.js file from client side

app.get('/events', serverEvent, function (req, res) {
	res.sse('test', "event with name test");
	res.sse('default event name message');
});
```
Or you can use it like this:

```javascript
app.get('/events', function (req, res) {
	serverEvent(req, res); // this will work the same as using it as middleware

	res.sse('test', "event with name test");
	res.sse('default event name message');
});
```

## Client:

```html
<script type="text/javascript" src="http://localhost:8080/sse.js"></script>
<script type="text/javascript">
	// in this example sse.js will be fetched from server
	// you can also copy client.js file and serve it from different place
	var sse = new ServerSentEvent('events');

	sse.on('test', function (data) {
		console.log('test', data);
	});

	sse.on('message', function (data) {
		console.log('message', data);
	});
</script>
```

##### Functions:

- *on([name], callback)*
	
Adds event listener to event with provided name. If name is not provided it will be set to default name "message". Callback will return two arguments - data and event in the same order.

- *once([name], callback)*
	
Works the same way as "on", but the callback will be called only once. After that the event listener will be removed

- *removeListener([name], callback)*
	
Will remove event listener by name. You need to provide the same function witch were used in "on" function when the event was attached. If name is not provided it will be set to default name "message".

- *removeAllListeners([name])*
	
Removes all event listeners by name. If name is not provided will remove all event listeners that were set.

##
In next update will add support for older browsers and IE

## Tests
Will be coming soon

## License 

(The MIT License)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
