# Javascript API Documentation

API for WebSockets is accessible in global object `window.IPub.WebSockets.WAMP`.

## Include JavaScript files

You have co copy this two files to your document root:

```javascript
/vendor/IPub/websockets-wamp/public/js/autobahn.min.js
/vendor/IPub/websockets-wamp/public/js/ipub.websockets.wamp.js
```

or you could use **bower** components installer or other ways how to include statis files into your webpage.

## Start using client WebSockets API

Once the javascript is included, you can start using IPub.WebSockets to interact with the web socket server.

A *IPub.WebSockets.WAMP* object is made available in the global scope of the page. This can be used to connect to the server as follows:

```javascript
var webSocket = IPub.WebSockets.WAMP.initialize('ws://127.0.0.1:8080');
```

The following commands are available to a IPub.WebSockets.WAMP object returned by IPub.WebSockets.WAMP.initialize.

### IPub.WebSockets.WAMP.on(event, callback)

This allows you to listen for events called by the server. The only events fired currently are **"socket/connect"** and **"socket/disconnect"**.

```javascript
var webSocket = IPub.WebSockets.initialize('wamp', 'ws://127.0.0.1:8080');

webSocket.on('socket/connect', function(session){
    // Session is an Autobahn JS WAMP session.

    console.log('Successfully Connected!');
});

webSocket.on('socket/disconnect', function(error){
    // Error provides you with some insight into the disconnection: error.reason and error.code

    console.log('Disconnected for ' + error.reason + ' with code ' + error.code);
})
```

Clients subscribe to "Topics". Clients publish to those same topics. When this occurs, anyone subscribed will be notified if server broadcast some message.

For a more in depth description of PubSub architecture go and check [Autobahn JS PubSub Documentation](http://autobahn.ws/js/reference_wampv1.html)

* `session.subscribe(topic, function(uri, payload))`
* `session.unsubscribe(topic)`
* `session.publish(topic, event, exclude, eligible)`

These methods are all fairly straightforward, here's an example on using them:

```javascript
webSocket.on('socket/connect', function(session){

    // The callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("acme/channel", function(uri, payload){
        console.log("Received message", payload.msg);
    });

    session.publish('your/channel', 'This is a message!');
})
```
