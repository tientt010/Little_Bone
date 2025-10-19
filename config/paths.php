<?php
// Đường dẫn gốc của ứng dụng
define('BASE_PATH', str_replace('\\', '/', dirname(__DIR__)));
define('ROOT_PATH', BASE_PATH);

// Đường dẫn đến thư mục chứa file index.php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectFolder = trim($scriptDir, '/');

// Define constants
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('VIEW_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');
define('MIDDLEWARE_PATH', APP_PATH . '/middleware');
