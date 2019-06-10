<?php

namespace Test\lib;

class Router extends ContainerAware
{
    private $request;

    private $config;

    /**
     * Router constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function match()
    {
        if (null === $this->config) {
            $this->config = $this->get('app')->getRouterConfig();
        }
        $requestUri = $this->request->getRequestUri();
        foreach ($this->config as $name => $data) {
            if (\preg_match(sprintf('~^%s$~', $data->route), $requestUri)) {
                $this->request->setControllerClass(
                    $data->controller
                );

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $routeName
     *
     * @return string
     * @throws \Exception
     */
    public function getUrlByRouteName($routeName)
    {
        if (isset($this->config->$routeName)) {
            return $this->config->$routeName->route;
        }

        throw new \Exception(sprintf('Route name "%s" doesn\'t exest', $routeName));
    }

    /**
     * @param string $url
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getRouteNameByUrl($url)
    {
        foreach ($this->config as $routeName => $routeInfo) {
            if ($routeInfo->route === $url) {
                return $routeName;
            }
        }

        throw new \Exception(sprintf('Url "%s" doesn\'t in router config', $url));
    }
}