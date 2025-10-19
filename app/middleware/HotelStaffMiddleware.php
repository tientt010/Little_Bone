<?php

namespace app\Middleware;

use Closure;

class HotelStaffMiddleware
{
    public function handle(Closure $next)
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'hotel_staff') {

            // Lấy URI và loại bỏ base path nếu có
            $requestUri = $_SERVER['REQUEST_URI'];
            $basePath = '/little_bone_2';

            // Nếu URI bắt đầu với base path, loại bỏ nó
            if (strpos($requestUri, $basePath) === 0) {
                $requestUri = substr($requestUri, strlen($basePath));
            }

            $_SESSION['redirect_url'] = $requestUri;

            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập với quyền nhân viên khách sạn để truy cập trang này'
            ];

            header('Location: ' . SITE_URL . '/login');
            exit;
        }
        return $next();
    }
}
