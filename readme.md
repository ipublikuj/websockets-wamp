# WebSockets WAMP

[![Build Status](https://img.shields.io/travis/iPublikuj/websockets-wamp.svg?style=flat-square)](https://travis-ci.org/iPublikuj/websockets-wamp)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/iPublikuj/websockets-wamp.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/websockets-wamp/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/iPublikuj/websockets-wamp.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/websockets-wamp/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/websockets-wamp.svg?style=flat-square)](https://packagist.org/packages/ipub/websockets-wamp)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/websockets-wamp.svg?style=flat-square)](https://packagist.org/packages/ipub/websockets-wamp)
[![License](https://img.shields.io/packagist/l/ipub/websockets-wamp.svg?style=flat-square)](https://packagist.org/packages/ipub/websockets-wamp)

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
