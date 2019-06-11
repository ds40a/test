<?php

/**  */
namespace Test\lib;

/**
 * Class ContainerAware
 */
class ContainerAware
{
    protected $container;

    /**
     * @param ServiceContainer $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @param $key
     *
     * @return object
     * @throws \Exception
     */
    public function get($key)
    {
        return $this->container->get($key);
    }
}
