<?php

/**  */
namespace Test\lib;

/**
 * Class App
 */
class App
{
    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    /**
     * @var object
     */
    private $routerConfig;

    /**
     * @var object
     */
    private $appConfig;

    /**
     * @var array
     */
    private $translations;

    /**
     * @var string
     */
    private $thisDir = __DIR__;

    /**
     * App constructor.
     */
    public function __construct()
    {
        ini_set('display_errors', 'Off');
        \set_exception_handler([$this, 'exceptionHandler']);
        $this->initServiceContainer();
        Model::$connection = $this->get('db')->getConnection();
    }

    /**
     * @param $key
     *
     * @return object
     */
    public function get($key)
    {
        return $this->serviceContainer->get($key);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $router = new Router($request);
        $router->setContainer($this->serviceContainer);
        $this->serviceContainer->set('router', $router);
        $this->get('translator')->setLang($this->get('session')->getUserLang());
        if ($router->match()) {
            list($controllerClass, $action) = \explode(':', $request->getControllerClass());
            $controller = new $controllerClass($request);
            $controller->setContainer($this->serviceContainer);
            $response = $controller->$action($request);
            if ($response instanceof Response) {
                return $response;
            }
            $result = new Response($response);
            $result->setContainer($this->serviceContainer);

            return $result;
        }

        $translator = $this->serviceContainer->get('translator');

        return new Response($translator->trans('Sorry! Requested page doesn\'t found' ), 404);
    }

    public function close()
    {
        $this->get('session')->close();
    }

    /**
    * @param \Exception|\Throwable $exception
    * @param array                 $error
    */
    public function exceptionHandler($exception, array $error = null)
    {
        ob_get_clean();
        $this->serviceContainer->get('logger')->exception($exception->getMessage());
        header(' 500 Internal Server Error', true, 500);
        echo "Internal app error";
        exit(0);
    }

    public function getRootDir()
    {
        return   $this->thisDir.'/..';
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return   $this->thisDir.'/../Resource/Config';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return   $this->thisDir.'/../../logs';
    }

    /**
     * @return string
     */
    public function getImagesDir()
    {
        return   $this->thisDir.'/../../web/upload/image';
    }

    /**
     * @return string
     */
    public function getImagesPath()
    {
        return   '/upload/image';
    }

    /**
     * @return object
     */
    public function getRouterConfig()
    {
        if (!$this->routerConfig) {
            $this->routerConfig = \json_decode(\file_get_contents($this->getConfigDir().'/routes.json'));
        }

        return $this->routerConfig;
    }

    /**
     * @return object
     */
    public function getAppConfig()
    {
        if (!$this->appConfig) {
            $this->appConfig = \json_decode(\file_get_contents($this->getConfigDir().'/params.json'));
        }

        return $this->appConfig;
    }

    public function getTranslationsDir()
    {
        return $this->thisDir.'/../Resource/translations';
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        if (!$this->translations) {
            $this->translations = \json_decode(\file_get_contents($this->getTranslationsDir().'/translations.json'), true);
        }

        return $this->translations;
    }

    /**
     *
     */
    protected function initServiceContainer()
    {
        $this->serviceContainer = new ServiceContainer();
        $this->serviceContainer->set('app', $this);

        $config = $this->getAppConfig();
        $this->serviceContainer->set('db', new Db(
            $config->db->host,
            $config->db->port,
            $config->db->dbName,
            $config->db->user,
            $config->db->password
        ));

        $translator = new Translator();
        if ($translator instanceof ContainerAware) {
            $translator->setContainer($this->serviceContainer);
        }
        $translator->init($this->getTranslations());
        $this->serviceContainer->set('translator', $translator);

        $logger = new Logger();
        if ($logger instanceof ContainerAware) {
            $logger->setContainer($this->serviceContainer);
        }
        $this->serviceContainer->set('logger', $logger);

        $session = new Session();
        if ($session instanceof ContainerAware) {
            $session->setContainer($this->serviceContainer);
        }
        $this->serviceContainer->set('session', $session);

        $render = new Render();
        if ($render instanceof ContainerAware) {
            $render->setContainer($this->serviceContainer);
        }
        $this->serviceContainer->set('render', $render);
    }
}