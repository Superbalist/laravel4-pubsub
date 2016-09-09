# laravel4-pubsub

A Pub-Sub abstraction for Laravel 4.

[![Author](http://img.shields.io/badge/author-@superbalist-blue.svg?style=flat-square)](https://twitter.com/superbalist)
[![Build Status](https://img.shields.io/travis/Superbalist/laravel4-pubsub/master.svg?style=flat-square)](https://travis-ci.org/Superbalist/laravel4-pubsub)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/superbalist/laravel4-pubsub.svg?style=flat-square)](https://packagist.org/packages/superbalist/laravel4-pubsub)
[![Total Downloads](https://img.shields.io/packagist/dt/superbalist/laravel4-pubsub.svg?style=flat-square)](https://packagist.org/packages/superbalist/laravel4-pubsub)

This package is a wrapper bridging [php-pubsub](https://github.com/Superbalist/php-pubsub) into Laravel 4.

For **Laravel 5** support, use the package https://github.com/Superbalist/laravel-pubsub

The following adapters are supported:
* Local
* /dev/null
* Redis
* Kafka (see separate installation instructions below)
* Google Cloud

## Installation

```bash
composer require superbalist/laravel4-pubsub
```

TODO: documentation pending