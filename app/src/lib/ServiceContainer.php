<?php
/**
 * Project app.
 * User: ds40a
 * Date: 26.05.19
 * Time: 13:11
 */

namespace Test\lib;

/**
 * Class Container
 */
class ServiceContainer
{
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
}
