# WebSockets WAMP

[![Build Status](https://badgen.net/github/checks/ipublikuj/websockets-wamp/master?cache=300&style=flast-square)](https://github.com/ipublikuj/websockets-wamp)
[![Licence](https://badgen.net/packagist/license/ipub/websockets-wamp?cache=300&style=flast-square)](https://github.com/ipublikuj/websockets-wamp/blob/master/LICENSE.md)
[![Code coverage](https://badgen.net/coveralls/c/github/ipublikuj/websockets-wamp?cache=300&style=flast-square)](https://coveralls.io/github/ipublikuj/websockets-wamp)

![PHP](https://badgen.net/packagist/php/ipub/websockets-wamp?cache=300&style=flast-square)
[![Downloads total](https://badgen.net/packagist/dt/ipub/websockets-wamp?cache=300&style=flast-square)](https://packagist.org/packages/ipub/websockets-wamp)
[![Latest stable](https://badgen.net/packagist/v/ipub/websockets-wamp/latest?cache=300&style=flast-square)](https://packagist.org/packages/ipub/websockets-wamp)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

Extension for implementing WAMP protocol into [ipub/websockets](https://github.com/iPublikuj/websockets) 

## Installation

The best way to install ipub/websockets-wamp is using [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/websockets-wamp
```

After that you have to register extension in config.neon.

```neon
extensions:
	webSocketsWAMP: IPub\WebSocketsWAMP\DI\WebSocketsWAMPExtension
```

## Documentation

Learn how to create WAMP application & integrate it into websockets in [documentation](https://github.com/iPublikuj/websockets-wamp/blob/master/docs/en/index.md).

***
Homepage [https://www.ipublikuj.eu](https://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/websockets-wamp](http://github.com/iPublikuj/websockets-wamp).
