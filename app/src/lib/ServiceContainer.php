<?php

/**  */
namespace Test\lib;

/**
 * Class Container
 */
class ServiceContainer
{
    /** @var array  */
    private $services = [];

    /**
     * @param $key
     *
     * @return object
     *
     * @throws \Exception
     */
    public function get($key)
    {
        if (!\array_key_exists($key, $this->services)) {
            throw new \Exception(sprintf("Service '%s' doesn't exists", $key));
        }

        return $this->services[$key];
    }

    /**
     * @param $key
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function set($key, $instance)
    {
        return $this->services[$key] = $instance;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return \array_key_exists($key, $this->services);
    }
}
