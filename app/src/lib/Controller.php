<?php

namespace Test\lib;
use Test\Model\UserModel;

/**
 * Class Controller
 */
class Controller extends ContainerAware
{
    private $request;

    private $user;

    /**
     * Controller constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $key
     *
     * @return object
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $template
     * @param array  $vars
     *
     * @return string
     */
    public function render($template, $vars=[])
    {
        return $this->get('render')->render($template, \array_merge(['view' => $this], $vars));
    }

    /**
     * @return object
     */
    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }

        $this->user = new UserModel();
        if ($userId = $this->get('session')->getUserId()) {
            if ($user = $this->user->find($userId)) {
                $this->user->set($user);
            }
        }

        return $this->user;
    }

    /**
     * @param $routeName
     */
    public function redirect($routeName)
    {
        \header(sprintf('Location: %s', $this->get('router')->getUrlByRouteName($routeName)));
        exit();
    }

    /**
     * @param $url
     */
    public function redirectToUrl($url)
    {
        \header(sprintf('Location: %s', $url));
        exit();
    }

    public function translate($key)
    {
        return $this->get('translator')->trans($key);
    }
}
