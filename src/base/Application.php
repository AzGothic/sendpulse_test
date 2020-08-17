<?php
namespace app\base;

use app\base\db\Connector;
use app\base\db\drivers\DbDriverInterface;
use app\base\helpers\ArrayHelper;

class Application
{
    /**
     * Application Config data
     * @var array $config
     */
    public static $config = [];

    /**
     * DB connect
     * @var DbDriverInterface $db
     */
    public static $db;

    /**
     * Composer Loader instance
     * @var object $loader
     */
    public static $loader = null;

    /**
     * Request
     * @var Request $request
     */
    public static $request;

    /**
     * Response
     * @var Response $response
     */
    public static $response;

    /**
     * Requested Application Controller/Action
     * @var array $route
     */
    public static $route = [
        'controller' => 'index',
        'action'     => 'index',
    ];

    /**
     * Application initialization
     * @param bool $runAction
     * @throws DbException
     * @throws LogicException
     */
    public static function init($runAction = true)
    {
        static::initConfig();
        static::initDB();
        static::initRequest();
        static::route();
        static::initResponse();
        if ($runAction) {
            static::runAction();
            static::$response->render();
        }
    }


    /**
     * Simple Config initialization
     * @return void
     */
    protected static function initConfig()
    {
        $configFile      = APP_PATH . 'config/main.php';
        $configLocalFile = APP_PATH . 'config/main-local.php';

        $config = require $configFile;
        if (file_exists($configLocalFile)) {
            $config = ArrayHelper::merge($config, require $configLocalFile);
        }

        static::$config = $config;

        if (!empty(static::$config['timezone'])) {
            date_default_timezone_set(static::$config['timezone']);
        }
    }

    /**
     * Simple DB init
     * @return void
     * @throws DbException
     */
    protected static function initDB()
    {
        static::$db = Connector::getConnect(static::$config['db']);
    }

    /**
     * Simple Request init
     * @return void
     */
    protected static function initRequest()
    {
        $requestClass = 'app\\base\\Request' . ucfirst(APP_TYPE);
        static::$request = new $requestClass();
    }

    /**
     * Simple Response init
     * @return void
     */
    protected static function initResponse()
    {
        $responseClass = 'app\\base\\Response' . ucfirst(APP_TYPE);
        static::$response = new $responseClass();
    }

    /**
     * Init Routing
     * @return void
     */
    protected static function route()
    {
        $routeMethod = 'route' . ucfirst(APP_TYPE);
        static::$routeMethod();
    }

    /**
     * Simple CLI Routing
     * @return void
     */
    protected static function routeCli()
    {
        $route = !empty(static::$request->_SERVER['argv'][1]) ? static::$request->_SERVER['argv'][1] : "";
        $routeParts = explode('/', $route);
        static::$route['controller'] = !(empty($routeParts[0])) ? strtolower($routeParts[0]) : 'index';
        static::$route['action']     = !(empty($routeParts[1])) ? strtolower($routeParts[1]) : 'index';
    }

    /**
     * Simple CLI Routing
     * @return void
     */
    protected static function routeWeb()
    {
        $uri = !empty(static::$request->_SERVER['REQUEST_URI']) ? static::$request->_SERVER['REQUEST_URI'] : "";
        $route = trim(parse_url($uri, PHP_URL_PATH), '/');
        $routeParts = explode('/', $route);
        static::$route['controller'] = !(empty($routeParts[0])) ? strtolower($routeParts[0]) : 'index';
        static::$route['action']     = !(empty($routeParts[1])) ? strtolower($routeParts[1]) : 'index';
    }

    /**
     * Simple Action runner
     * @return void
     * @throws LogicException
     */
    protected static function runAction()
    {
        $controllerClass = 'app\\controllers\\' . APP_TYPE . '\\' . ucfirst(static::$route['controller']) . 'Controller';
        if (!class_exists($controllerClass)) {
            throw new LogicException("Undefined controller class '$controllerClass'");
        }
        $controllerInstance = new $controllerClass();

        $actionMethod = ucfirst(static::$route['action']) . 'Action';
        if (!method_exists($controllerInstance, $actionMethod)) {
            throw new LogicException("Undefined action method '$controllerClass::$actionMethod()'");
        }

        $controllerInstance->$actionMethod();
    }
}