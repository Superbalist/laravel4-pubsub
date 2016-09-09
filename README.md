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

The package has a default configuration built-in.

To customize the configuration file, publish the package configuration using Artisan.
```bash
php artisan config:publish superbalist/laravel4-pubsub
```

You can then edit the generated config at `app/config/packages/superbalist/laravel4-pubsub/config.php`.

Register the service provider in app.php
```php
'providers' => [
    // ...
    'Superbalist\Laravel4PubSub\PubSubServiceProvider'
]
```

Register the facade in app.php
```php
'aliases' => [
    // ...
    'PubSub' => 'Superbalist\Laravel4PubSub\PubSubFacade',
]
```

## Kafka Adapter Installation

Please note that whilst the package is bundled with support for the [php-pubsub-kafka](https://github.com/Superbalist/php-pubsub-kafka)
adapter, the adapter is not included by default.

This is because the KafkaPubSubAdapter has an external dependency on the `librdkafka c library` and the `php-rdkafka`
PECL extension.

If you plan on using this adapter, you will need to install these dependencies by following these [installation instructions](https://github.com/Superbalist/php-pubsub-kafka).

You can then include the adapter using:
```bash
composer require superbalist/php-pubsub-kafka
```

## Usage

```php
// get the pub-sub manager
$pubsub = app('pubsub');

// note: function calls on the manager are proxied through to the default connection
// eg: you can do this on the manager OR a connection
$pubsub->publish('channel_name', 'message');

// get the default connection
$pubsub = app('pubsub.connection');
// or
$pubsub = app(\Superbalist\PubSub\PubSubAdapterInterface::class);

// get a specific connection
$pubsub = app('pubsub')->connection('redis');

// publish a message
// the message can be a string, array, json string, bool, object - anything which can be serialized
$pubsub->publish('channel_name', 'this is where your message goes');
$pubsub->publish('channel_name', ['key' => 'value']);
$pubsub->publish('channel_name', '{"key": "value"}');
$pubsub->publish('channel_name', true);

// subscribe to a channel
$pubsub->subscribe('channel_name', function ($message) {
    var_dump($message);
});

// all the aboce commands can also be done using the facade
PubSub::connection('kafka')->publish('channel_name', 'Hello World!');

PubSub::connection('kafka')->subscribe('channel_name', function ($message) {
    var_dump($message);
});
```

## Creating a Subscriber

A lot of pub-sub adapters will contain blocking `subscribe()` calls, so these commands are best run as daemons running
as a [supervisor](http://supervisord.org) process.

This is a sample subscriber written as an artisan command.
```php
<?php

namespace App\Commands;

use App;
use Illuminate\Console\Command;

class MyExampleSubscriber extends Command
{
    /**
     * The name and signature of the subscriber command.
     *
     * @var string
     */
    protected $name = 'subscriber:name';

    /**
     * The subscriber description.
     *
     * @var string
     */
    protected $description = 'PubSub subscriber for ________';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $pubsub = App::make('pubsub');
        $pubsub->subscribe('channel_name', function ($message) {

        });
    }
}
```

### Kafka Subscribers ###

For subscribers which use the `php-pubsub-kafka` adapter, you'll likely want to change the `consumer_group_id` per
subscriber.

To do this, you need to use the `PubSubConnectionFactory` to create a new connection per subscriber.  This is because
the `consumer_group_id` cannot be changed once a connection is created.

Here is an example of how you can do this:

```php
<?php

namespace App\Commands;

use App;
use Config;
use Illuminate\Console\Command;

class MyExampleKafkaSubscriber extends Command
{
    /**
     * The name and signature of the subscriber command.
     *
     * @var string
     */
    protected $name = 'subscriber:name';

    /**
     * The subscriber description.
     *
     * @var string
     */
    protected $description = 'PubSub subscriber for ________';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $config = Config::get('laravel4-pubsub::connections.kafka');
        $config['consumer_group_id'] = self::class;
        $factory = App::make('pubsub.factory');
        $pubsub = $factory->make('kafka', $config);
        $pubsub->subscribe('channel_name', function ($message) {

        });
    }
}
```

## Adding a Custom Driver

Please see the [php-pubsub](https://github.com/Superbalist/php-pubsub) documentation  **Writing an Adapter**.

To include your custom driver, you can call the `extend()` function.

```php
$manager = app('pubsub');
$manager->extend('custom_connection_name', function ($config) {
    // your callable must return an instance of the PubSubAdapterInterface
    return new MyCustomPubSubDriver($config);
});

// get an instance of your custom connection
$pubsub = $manager->connection('custom_connection_name');
```