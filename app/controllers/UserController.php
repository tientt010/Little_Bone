<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\User;
use app\Models\Booking;

class UserController extends BaseController
{
    private $user;
    private $booking;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
        $this->booking = new Booking();
    }

    public function profile()
    {
        try {
            // Lấy thông tin user từ session
            $userId = $_SESSION['user_id'];
            $user = $this->user->find($userId);

            if (!$user) {
                throw new \Exception('Không tìm thấy thông tin người dùng');
            }

            $recentBookings = $this->booking->getUserBookedHotels(
                $userId,
                3
            );
            foreach ($recentBookings as &$booking) {
                $booking['hotel_image'] = SITE_URL . '/public/images/hotels/' . $booking['hotel_id'] . '/main.jpg';
            }

            $favoriteHotels = $this->user->getFavoriteHotels($userId);

            return $this->view('user/profile', [
                'user' => $user,
                'recentBookings' => $recentBookings,
                'favoriteHotels' => $favoriteHotels,
                'pageTitle' => 'Thông tin tài khoản'
            ]);
        } catch (\Exception $e) {

            $this->setFlash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    // Cập nhật thông tin cá nhân
    public function updateProfile()
    {
        if (!$this->isPost()) {
            return $this->redirect('/user/profile');
        }

        $userId = $_SESSION['user_id'];

        $data = [
            'full_name' => $this->getPost('full_name'),
            'email' => $this->getPost('email'),
            'phone' => $this->getPost('phone'),
            'address' => $this->getPost('address'),
            'birth_date' => $this->getPost('birth_date'),
            'gender' => $this->getPost('gender')
        ];

        $success = $this->user->updateProfile($userId, $data);

        if ($success) {
            $_SESSION['user_name'] = $data['full_name'];

            $this->setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            $errors = $this->user->getErrors();
            $errorMessage = reset($errors);
            $this->setFlash('error', 'Cập nhật thông tin thất bại: ' . $errorMessage);
        }

        return $this->redirect('/user/profile');
    }

    public function updatePassword()
    {
        if (!$this->isPost()) {
            return $this->redirect('/user/profile');
        }

        $userId = $_SESSION['user_id'];
        $currentPassword = $this->getPost('current_password');
        $newPassword = $this->getPost('new_password');
        $confirmPassword = $this->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            $this->setFlash('error', 'Mật khẩu xác nhận không khớp');
            return $this->redirect('/user/profile');
        }

        $success = $this->user->changePassword($userId, $currentPassword, $newPassword);

        if ($success) {
            $this->setFlash('success', 'Đổi mật khẩu thành công!');
        } else {
            $errors = $this->user->getErrors();
            $errorMessage = reset($errors);
            $this->setFlash('error', 'Đổi mật khẩu thất bại: ' . $errorMessage);
        }

        return $this->redirect('/user/profile');
    }

    public function updateAvatar()
    {
        if (!$this->isPost()) {
            return $this->redirect('/user/profile');
        }

        $userId = $_SESSION['user_id'];

        try {
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Không thể tải lên ảnh đại diện: ' . $this->getUploadErrorMessage($_FILES['avatar']['error']));
            }

            $file = $_FILES['avatar'];

            // Kiểm tra loại file
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                throw new \Exception('File tải lên không phải là ảnh hợp lệ');
            }

            $mimeType = $imageInfo['mime'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF');
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                throw new \Exception('Kích thước file không được vượt quá 2MB');
            }

            $uploadDir = ROOT_PATH . '/public/images/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Đặt tên file mới để tránh trùng lặp
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new \Exception('Không thể lưu file ảnh. Vui lòng thử lại sau.');
            }

            // Cập nhật đường dẫn avatar trong database
            $result = $this->user->updateAvatar($userId, $fileName);

            if (!$result) {
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                throw new \Exception('Không thể cập nhật ảnh đại diện trong cơ sở dữ liệu');
            }
            $_SESSION['user_avatar'] = $fileName; // Cập nhật avatar trong session

            $this->setFlash('success', 'Cập nhật ảnh đại diện thành công');
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }

        return $this->redirect('/user/profile');
    }

    public function removeFavorite()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này']);
            return;
        }

        if (!isset($_POST['hotel_id']) || !is_numeric($_POST['hotel_id'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Thông tin không hợp lệ']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $hotelId = (int)$_POST['hotel_id'];

        try {
            $result = $this->user->removeFavoriteHotel($userId, $hotelId);

            $this->sendJsonResponse(['success' => $result, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } catch (\Exception $e) {

            $this->sendJsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xử lý yêu cầu']);
        }
    }

    // Trả về thông điệp lỗi dựa trên mã lỗi upload
    private function getUploadErrorMessage($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá upload_max_filesize trong php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá MAX_FILE_SIZE trong form HTML)',
            UPLOAD_ERR_PARTIAL => 'Upload chưa hoàn thành',
            UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
            UPLOAD_ERR_NO_TMP_DIR => 'Thư mục tạm không tồn tại',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào ổ đĩa',
            UPLOAD_ERR_EXTENSION => 'Upload bị dừng bởi extension',
        ];

        return isset($errors[$errorCode]) ? $errors[$errorCode] : 'Lỗi không xác định';
    }

    public function getStatusBadgeClass($status)
    {
        switch ($status) {
            case 'pending':
                return 'warning';
            case 'confirmed':
                return 'success';
            case 'cancelled':
                return 'danger';
            case 'completed':
                return 'info';
            default:
                return 'secondary';
        }
    }

    // Hủy đặt phòng
    public function cancelBooking()
    {
        if (!isset($_SESSION['user_id'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này']);
        }

        if (!isset($_POST['booking_id']) || !is_numeric($_POST['booking_id'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Thông tin không hợp lệ']);
        }

        $userId = $_SESSION['user_id'];
        $bookingId = (int)$_POST['booking_id'];

        try {
            $result = $this->booking->cancelBooking($bookingId);

            return $this->jsonResponse(['success' => $result, 'message' => 'Đã hủy đặt phòng']);
        } catch (\Exception $e) {

            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xử lý yêu cầu']);
        }
    }

    public function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Chờ xác nhận';
            case 'confirmed':
                return 'Đã xác nhận';
            case 'cancelled':
                return 'Đã hủy';
            case 'completed':
                return 'Hoàn thành';
            default:
                return 'Không xác định';
        }
    }
}
