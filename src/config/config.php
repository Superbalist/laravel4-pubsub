<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    |
    | The default pub-sub connection to use.
    |
    | Supported: "/dev/null", "local", "redis", "kafka", "gcloud", "http"
    |
    */

    'default' => 'redis',

    /*
    |--------------------------------------------------------------------------
    | Pub-Sub Connections
    |--------------------------------------------------------------------------
    |
    | The available pub-sub connections to use.
    |
    | A default configuration has been provided for all adapters shipped with
    | the package.
    |
    */

    'connections' => [

        '/dev/null' => [
            'driver' => '/dev/null',
        ],

        'local' => [
            'driver' => 'local',
        ],

        'redis' => [
            'driver' => 'redis',
            'scheme' => 'tcp',
            'host' => 'localhost',
            'password' => null,
            'port' => 6379,
            'database' => 0,
            'read_write_timeout' => 0,
        ],

        'kafka' => [
            'driver' => 'kafka',
            'consumer_group_id' => 'php-pubsub',
            'brokers' => 'localhost',
        ],

        'gcloud' => [
            'driver' => 'gcloud',
            'project_id' => null,
            'key_file' => null,
            'client_identifier' => null,
            'auto_create_topics' => true,
            'auto_create_subscriptions' => true,
            'auth_cache' => null, // eg: \Google\Auth\Cache\MemoryCacheItemPool::class,
        ],

        'http' => [
            'driver' => 'http',
            'uri' => null,
            'subscribe_connection' => 'redis',
        ],

    ],

];
