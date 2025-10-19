<?php

namespace app\Middleware;


class AuthMiddleware
{
    public function handle($next)
    {

        if (!isset($_SESSION['user_id'])) {
            // Lấy URI và loại bỏ base path nếu có
            $requestUri = $_SERVER['REQUEST_URI'];
            $basePath = '/little_bone_2';

            // Nếu URI bắt đầu với base path, loại bỏ nó
            if (strpos($requestUri, $basePath) === 0) {
                $requestUri = substr($requestUri, strlen($basePath));
            }

            $_SESSION['redirect_url'] = $requestUri;
            header('Location: ' . SITE_URL . '/login');
            exit;
        }

        return $next();
    }
}
