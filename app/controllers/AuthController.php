<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\User;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    public function login()
    {
        // Nếu đã đăng nhập thì redirect về trang chủ
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/');
        }

        if ($this->isPost()) {
            $loginId = $this->getPost('login_id');
            $password = $this->getPost('password');

            // Xác định loại thông tin đăng nhập
            $loginType = $this->determineLoginType($loginId);

            $user = $this->userModel->authenticateByType($loginId, $password, $loginType);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_avatar'] = $user['avatar'] ?? 'default.jpg'; // Lưu avatar vào session

                if ($user['role'] === 'hotel_staff') {
                    $hotelStaffModel = new \app\Models\HotelStaff();
                    $hotelAssignment = $hotelStaffModel->findOne(['user_id' => $user['id']]);

                    if ($hotelAssignment && isset($hotelAssignment['hotel_id'])) {
                        $_SESSION['hotel_id'] = (int)$hotelAssignment['hotel_id'];
                    }
                }

                if (isset($_SESSION['redirect_url'])) {
                    $redirectUrl = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    return $this->redirect($redirectUrl);
                }

                return $this->redirect('/');
            }

            $this->setFlash('error', 'Thông tin đăng nhập không chính xác');
        }

        return $this->view('auth/login');
    }

    // Xác định loại thông tin đăng nhập dựa trên định dạng
    private function determineLoginType($loginId)
    {
        if (is_numeric($loginId)) {
            return 'phone';
        }
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        return 'username';
    }

    // Xử lý đăng ký tài khoản mới
    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            return $this->redirect('/');
        }

        if ($this->isPost()) {
            $data = [
                'username' => $this->getPost('username'),
                'password' => $this->getPost('password'),
                'full_name' => $this->getPost('full_name'),

            ];

            // Validate password confirmation
            if ($this->getPost('password') !== $this->getPost('password_confirmation')) {
                $this->setFlash('error', 'Mật khẩu xác nhận không khớp');
                return $this->view('auth/register', ['data' => $data]);
            }

            $userId = $this->userModel->register($data);

            if ($userId) {
                $this->setFlash('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
                return $this->redirect('/login');
            }

            $errors = $this->userModel->getErrors();

            $errorMessage = reset($errors);
            $this->setFlash('error', $errorMessage);
            return $this->view('auth/register', ['data' => $data]);
        }

        return $this->view('auth/register');
    }

    // Xử lý đăng xuất
    public function logout()
    {
        session_destroy();
        return $this->redirect('/login');
    }

    // Xử ly xử lý chức năng quên mật khẩu
    public function forgotPassword()
    {
        $this->setFlash('info', 'Tính năng quên mật khẩu đang được bảo trì. Vui lòng liên hệ hỗ trợ nếu bạn cần đặt lại mật khẩu.');
        return $this->redirect('/login');
    }
}
