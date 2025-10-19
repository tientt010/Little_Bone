<?php

use core\ErrorHandler;
use core\Router;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load core configuration
require_once __DIR__ . '/config/paths.php';
require_once __DIR__ . '/config/namespaces.php';
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/routes.php';

// Debug
$logPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($logPath)) {
    mkdir($logPath, 0755, true);
}
$logFile = $logPath . DIRECTORY_SEPARATOR . 'debug.log';
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}
ini_set('log_errors', 1);
ini_set('error_log', $logFile);

// Log visitor IPs
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isStaticFile = preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/i', $requestUri);
if (!$isStaticFile) {
    $visitorIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    error_log("Visitor - IP: {$visitorIp} - " . date('Y-m-d H:i:s'));
}


require_once CORE_PATH . '/Router.php';
require_once CORE_PATH . '/ErrorHandler.php';

error_reporting(E_ALL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);

spl_autoload_register(function ($class) {
    $basePath = ROOT_PATH;

    $parts = explode('\\', $class);


    if (count($parts) > 1) {
        $className = array_pop($parts);
        $namespaceParts = array_map('strtolower', $parts);
        $parts = array_merge($namespaceParts, [$className]);
    }

    $classPath = implode(DIRECTORY_SEPARATOR, $parts) . '.php';
    $filePath = $basePath . DIRECTORY_SEPARATOR . $classPath;

    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }

    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $filePath = $basePath . DIRECTORY_SEPARATOR . $classPath;

    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }

    return false;
});

try {
    $router = new Router();

    foreach ($routes as $path => $handler) {
        $router->addRoute($path, $handler);
    }

    foreach ($middlewares as $path => $middleware) {
        $router->addMiddleware($path, $middleware);
    }


    $router->dispatch();
} catch (Exception $e) {

    ErrorHandler::handleException($e);
}
