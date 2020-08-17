<?php

use app\base\Application;

/** App Type definition */
defined('APP_TYPE') ||
define('APP_TYPE', 'web');

/** Error Reporting */
ini_set('display_errors', true);
error_reporting(E_ALL);

/** App Path definition */
defined('APP_PATH') ||
define('APP_PATH', __DIR__ . '/');

/** Register autoloader */
$loader = require APP_PATH . 'vendor/autoload.php';

/** Init Application */
Application::$loader = $loader;
Application::init();
