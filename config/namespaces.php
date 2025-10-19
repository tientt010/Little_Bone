<?php

// Namespace gốc cho các thành phần chính
define('NAMESPACE_APP', 'app');
define('NAMESPACE_CORE', 'core');
define('NAMESPACE_CONFIG', 'config');

// Namespace cho các thành phần con
define('NAMESPACE_CONTROLLERS', NAMESPACE_APP . '\\controllers');
define('NAMESPACE_MODELS', NAMESPACE_APP . '\\models');
define('NAMESPACE_MIDDLEWARE', NAMESPACE_APP . '\\middleware');

// Định nghĩa đường dẫn file tương ứng với namespace - kiểm tra trước khi định nghĩa
if (!defined('CONTROLLERS_PATH')) {
    define('CONTROLLERS_PATH', ROOT_PATH . '/app/controllers');
}

if (!defined('MODELS_PATH')) {
    define('MODELS_PATH', ROOT_PATH . '/app/models');
}

if (!defined('MIDDLEWARE_PATH')) {
    define('MIDDLEWARE_PATH', ROOT_PATH . '/app/middleware');
}

// Hàm helper chuyển namespace thành đường dẫn file
function namespaceToPath($namespace)
{
    return str_replace('\\', '/', $namespace) . '.php';
}
