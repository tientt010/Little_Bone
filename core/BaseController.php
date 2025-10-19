<?php

namespace core;

abstract class BaseController
{
    protected $viewPath = ROOT_PATH . '/app/views/';
    protected $layout = 'layouts/main';

    public function __construct()
    {
        // Bắt đầu session nếu chưa được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateCsrfToken();
        }
    }


    protected function view($view, $data = [])
    {
        try {
            extract($data);

            $data['csrf_token'] = $_SESSION['csrf_token'] ?? $this->generateCsrfToken();

            ob_start();

            $viewFile = $this->viewPath . $view . '.php';
            if (!file_exists($viewFile)) {
                throw new \Exception("View file not found: {$viewFile}");
            }
            require $viewFile;

            // Lấy nội dung view
            $content = ob_get_clean();

            $layoutFile = $this->viewPath . $this->layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout file not found: {$layoutFile}");
            }
            require $layoutFile;
        } catch (\Exception $e) {
            // Log lỗi


            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                echo '<div style="color:red;background:#fdd;padding:10px;border:1px solid #f00;">';
                echo '<h4>View Error</h4>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '</div>';
            } else {
                echo 'Đã xảy ra lỗi khi hiển thị trang.';
            }
        }
    }

    protected function sanitize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
        } else {
            $data = trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
        }
        return $data;
    }

    // Lấy thông tin người dùng hiện tại từ session
    protected function getCurrentUser($withDetails = false)
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        if ($withDetails && !isset($_SESSION['user_data'])) {
            // Load thông tin user từ database nếu chưa có trong session
            $userModel = new \App\Models\User();
            $_SESSION['user_data'] = $userModel->find($_SESSION['user_id']);
        }

        return $_SESSION['user_data'] ?? ['id' => $_SESSION['user_id']];
    }


    // Kiểm tra và xác thực CSRF token
    protected function validateCsrfToken($token = null)
    {
        if (!$token) {
            $token = $this->getPost('csrf_token');
        }

        if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->setFlash('error', 'Yêu cầu không hợp lệ. Vui lòng thử lại.');
            return false;
        }

        $_SESSION['csrf_token'] = $this->generateCsrfToken();
        return true;
    }

    // Sinh CSRF token mới
    private function generateCsrfToken()
    {
        return bin2hex(random_bytes(32));
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return array_map([$this, 'sanitize'], $_GET);
        }
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    // Lấy dữ liệu từ POST request
    protected function getPost($key = null, $default = null)
    {
        if (!isset($_POST) || empty($_POST)) {
        }

        if ($key === null) {
            return array_map([$this, 'sanitize'], $_POST);
        }

        if (isset($_POST[$key])) {
            $value = $_POST[$key];

            if (is_array($value)) {
                return $value;
            }

            return trim($value);
        }

        // Kiểm tra trong request body nếu là request API
        $inputData = file_get_contents('php://input');
        if (!empty($inputData)) {
            $jsonData = json_decode($inputData, true);
            if (isset($jsonData[$key])) {
                return $this->sanitize($jsonData[$key]);
            }
        }

        return $default;
    }

    // Lấy dữ liệu từ request (GET hoặc POST) theo key
    protected function getRequestData($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $this->sanitize($_POST[$key]);
        } elseif (isset($_GET[$key])) {
            return $this->sanitize($_GET[$key]);
        }
        return $default;
    }

    // Lấy tất cả dữ liệu từ form (POST request)
    protected function getAllPost($keys = null)
    {
        $data = [];
        $formData = $_POST;

        if ($keys) {
            foreach ($keys as $key) {
                if (isset($formData[$key])) {
                    $data[$key] = $this->sanitize($formData[$key]);
                }
            }
        } else {
            foreach ($formData as $key => $value) {
                if ($key !== 'csrf_token') {
                    $data[$key] = $this->sanitize($value);
                }
            }
        }

        return $data;
    }

    // Lấy tất cả dữ liệu từ request (GET và POST)
    protected function getAllRequestData($method = 'ANY')
    {
        $data = [];

        if ($method === 'ANY' || $method === 'GET') {
            foreach ($_GET as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
        }

        if ($method === 'ANY' || $method === 'POST') {
            foreach ($_POST as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }


    // Chuyển hướng đến URL khác
    protected function redirect($url, $statusCode = 303)
    {
        if (substr($url, 0, 1) === '/' && defined('SITE_URL')) {
            $url = SITE_URL . $url;
        }
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    // Trả về JSON response
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    // Thiết lập flash message
    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    // Lấy và xóa flash message
    protected function getFlash($type)
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    // Lấy file upload từ $_FILES
    protected function getFile($key, $index = null)
    {
        if (!isset($_FILES[$key])) {
            return null;
        }

        if ($index !== null && isset($_FILES[$key]['name'][$index])) {
            return [
                'name' => $_FILES[$key]['name'][$index],
                'type' => $_FILES[$key]['type'][$index],
                'tmp_name' => $_FILES[$key]['tmp_name'][$index],
                'error' => $_FILES[$key]['error'][$index],
                'size' => $_FILES[$key]['size'][$index]
            ];
        }

        return $_FILES[$key];
    }


    // Hiên thị lỗi 404
    protected function notFound($message = 'Không tìm thấy trang yêu cầu')
    {
        header('HTTP/1.0 404 Not Found');
        $this->view('errors/404', ['message' => $message]);
        exit;
    }

    // Hiển thị lỗi 403
    protected function forbidden($message = 'Bạn không có quyền truy cập trang này')
    {
        header('HTTP/1.0 403 Forbidden');
        $this->view('errors/403', ['message' => $message]);
        exit;
    }

    protected function sendJsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Xây dựng URL query string từ các tham số hiện tại và các tham số mới
    protected function buildQueryString($newParams = [], $excludeParams = [])
    {
        $params = [];
        if (!empty($_GET)) {
            $params = $_GET;
        }

        foreach ($newParams as $key => $value) {
            $params[$key] = $value;
        }

        foreach ($excludeParams as $param) {
            if (isset($params[$param])) {
                unset($params[$param]);
            }
        }

        $queryString = http_build_query($params);

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $path . ($queryString ? '?' . $queryString : '');
    }

    // Phương thức để trả về JSON response
    protected function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
