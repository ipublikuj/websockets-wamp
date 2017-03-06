# Quickstart

This extension is implementing WAMP protocol into [ipub/websockets](https://github.com/iPublikuj/websockets) 

## Installation

The best way to install ipub/websockets-zmq is using  [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/websockets-zmq
```

After that you have to register extension in config.neon.

```neon
extensions:
	webSocketsZMQ: IPub\WebSocketsZMQ\DI\WebSocketsZMQExtension
```

## Usage

As first step, you have to install & configure basic package [ipub/websockets](https://github.com/iPublikuj/websockets)

When everything is installed and configured, start web sockets server:

```sh
php web/index.php ipub:websockets:start
```

If everything is successful, you will see something similar to the following:

```sh
 +------------------+
 | WebSocket server |
 +------------------+


 ! [NOTE] Starting IPub\WebSockets

 ! [NOTE] Launching WebSockets WS Server on: localhost:8888
```

This means the web sockets server is now up and running ! 

### Controllers & routes

In WAMP version of web sockets, clients are subscribed to topics, which are something like routes.

```bash
/en/communication/chat/room/123
/en/communication/chat/room/456
...
```

Application will take this route and try to parse it to the parameters.

So as next step is to define [route](https://github.com/iPublikuj/websockets/blob/master/docs/en/index.md#define-routes) for new controller.

```neon
    # WebSockets server
    webSockets:
        routes:
            '/[!<locale [a-z]{2,4}>/]communication/<controller>/room/<roomId>' : 'ChatController:'
```

Once you have defined route, you could create your first controller, which will handle requested actions:

```php
namespace App\Controllers

class ChatController extends \IPub\WebSockets\Application\Controller\Controller
{
    public function actionSubscribe(IPub\WebSockets\Entities\Clients\IClient $client, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, int $roomId)
    {
        // All route parameters could be passes as action parameters

        $topic->broadcast->($client->getId() .' joined: '. $roomId);
    }

    public function actionUnsubscribe(IPub\WebSockets\Entities\Clients\IClient $client, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, int $roomId)
    {
        $topic->broadcast->($client->getId() .' left: '. $roomId);
    }

    public function actionPublish($event, IPub\WebSocketsWAMP\Entities\Topics\ITopic $topic, IPub\WebSockets\Entities\Clients\IClient $client)
    {
        // $event could be string or instance of \stdClass, it depends on what your client send to the server

        $topic->broadcast->($client->getId() .' is saying: '. $event);
    }
}
```

As you can see, there are defined 3 action methods:

* **actionSubscribe** is called when new client subscribe to an topic
* **actionUnsubscribe** is called when client unsubscribe from topic
* **actionPublish** is called when client publish to a topic

First two action could be omitted. 

#### Most important things in topic

##### Broadcast full definition

```php
Topic::broadcast($msg, array $exclude = array(), array $eligible = array());
```

Send a message to all the connections in this topic.

**Note :** `$exclude` and `$include` work with client ID available through `$client->getId()`

##### How iterate over topic subscribers ?

`Topic` implements `IteratorAggregate`, you can iterate over subscribers present in your topic. Client are reprensented by `IPub\WebSockets\Entities\Clients\IClient`

```php
/** @var IPub\WebSockets\Entities\Clients\IClient $client **/
foreach ($topic as $client) {
    //Do stuff ...
}
```

##### How send a message only to my current client ?

`$connection->event($topic, 'messageContent');`

##### How count the number of subscribers topic currently have ?

`Topic` implements `Countable` interface, you just have to do `count($topic)`

**From here, the websocket server is running ! Controllers are prepared. Now you should prepare client part.**

### Next Steps

For further documentations on how to use WebSocket, please continue with the client side setup.

* [Setup Client Javascript](https://github.com/iPublikuj/websockets-wamp/blob/master/public/readme.md)
