<?php

namespace Test\lib;


class Request extends ContainerAware
{
    /** @var array  */
    private $get;

    /** @var array  */
    private $post;

    /** @var array  */
    private $cookies;

    /** @var array  */
    private $files;

    /** @var array  */
    private $server;

    /** @var string */
    private $controllerClass;

    /**
     * Request constructor.
     *
     * @param array $get
     * @param array $post
     * @param array $cookies
     * @param array $files
     * @param array $server
     */
    public function __construct(array $get = array(), array $post = array(), array $cookies = array(), array $files = array(), array $server = array())
    {
        $this->get = $get;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return \explode('?', $this->server['REQUEST_URI'])[0];
    }

    /**
     * @return Request
     */
    public static function create()
    {
        return new self($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * @return array
     */
    public function query()
    {
        return $this->get;
    }

    /**
     * @return array
     */
    public function queryParam($name)
    {
        if (isset($this->get[$name])) {
            return $this->get[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function requestParam($name)
    {
        if (isset($this->post[$name])) {
            return $this->post[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function request()
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * @return array
     */
    public function files()
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param string $cls
     */
    public function setControllerClass($cls)
    {
        $this->controllerClass = $cls;
    }

    /**
     * @return string
     */
    public function getReferer()
    {
        return $this->server['HTTP_REFERER'];
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->server['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @param string $paramName
     *
     * @return string|null
     */
    public function get($paramName)
    {
        if (isset($this->get[$paramName])) {
            return $this->get[$paramName];
        } elseif (isset($this->post[$paramName])) {
            return $this->post[$paramName];
        }

        return null;
    }
}