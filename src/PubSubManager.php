<?php

namespace Superbalist\Laravel4PubSub;

use Illuminate\Container\Container;
use InvalidArgumentException;
use Superbalist\PubSub\PubSubAdapterInterface;

class PubSubManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PubSubConnectionFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @param Container $container
     * @param PubSubConnectionFactory $factory
     */
    public function __construct(Container $container, PubSubConnectionFactory $factory)
    {
        $this->container = $container;
        $this->factory = $factory;
    }

    /**
     * Return a pub-sub adapter instance.
     *
     * @param string $name
     * @return PubSubAdapterInterface
     */
    public function connection($name = null)
    {
        if ($name === null) {
            $name = $this->getDefaultConnection();
        }

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Make an instance of a pub-sub adapter interface.
     *
     * @param string $name
     * @return PubSubAdapterInterface
     */
    protected function makeConnection($name)
    {
        $config = $this->getConnectionConfig($name);

        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }

        if (!isset($config['driver'])) {
            throw new InvalidArgumentException(
                sprintf('The pub-sub connection [%s] is missing a "driver" config var.', $name)
            );
        }

        return $this->factory->make($config['driver'], array_except($config, ['driver']));
    }

    /**
     * Return the pubsub config for the given connection.
     *
     * @param string $name
     * @return array
     */
    protected function getConnectionConfig($name)
    {
        $connections = $this->getConfig()['connections'];
        if (!isset($connections[$name])) {
            throw new InvalidArgumentException(sprintf('The pub-sub connection [%s] is not configured.', $name));
        }

        $config = $connections[$name];

        return $config;
    }

    /**
     * Return the pubsub config array.
     *
     * @return array
     */
    protected function getConfig()
    {
        $config = $this->container->make('config'); /** @var \Illuminate\Config\Repository $config */
        return $config->get('laravel4-pubsub::config');
    }

    /**
     * Return the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->getConfig()['default'];
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     */
    public function setDefaultConnection($name)
    {
        $config = $this->container->make('config'); /** @var \Illuminate\Config\Repository $config */
        $config->set('laravel4-pubsub::default', $name);
    }

    /**
     * Register an extension connection resolver.
     *
     * @param string $name
     * @param callable $resolver
     */
    public function extend($name, callable $resolver)
    {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Return all registered extension connection resolvers.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Return all the created connections.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
