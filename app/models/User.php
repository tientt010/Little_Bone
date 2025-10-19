<?php

namespace app\Models;

use core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'address',
        'birth_date',
        'gender',
        'avatar',
        'role'
    ];

    protected $rules = [
        'username' => 'required|min:3|max:50',
        'email' => 'email',
        'password' => 'required|min:6',
        'full_name' => 'required|max:100',
        'phone' => 'max:20',
        'address' => 'max:255',
        'gender' => 'in:male,female,other',
        'role' => 'in:traveler,hotel_staff,website_staff'
    ];

    // Tìm theo email
    public function findByEmail($email)
    {
        return $this->where('email', $email)->get()[0] ?? false;
    }

    // Đăng ký user mới
    public function register($data)
    {

        if (!$this->validate($data, $this->rules)) {
            return false;
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->create($data);
    }

    // Xác thực đăng nhập
    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            $this->errors['email'] = 'Email không tồn tại';
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            $this->errors['password'] = 'Mật khẩu không đúng';
            return false;
        }

        unset($user['password']);
        return $user;
    }

    // Xác thực theo loại thông tin đăng nhập
    public function authenticateByType($loginId, $password, $type)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";

        switch ($type) {
            case 'phone':
                $sql .= "phone = ?";
                break;
            case 'email':
                $sql .= "email = ?";
                break;
            default:
                $sql .= "username = ?";
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$loginId]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->errors['login'] = 'Tài khoản không tồn tại';
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            $this->errors['password'] = 'Mật khẩu không đúng';
            return false;
        }

        unset($user['password']);
        return $user;
    }

    // Cập nhật thông tin người dùng
    public function updateProfile($id, $data)
    {
        try {
            // Loại bỏ các trường không được phép cập nhật
            unset($data['password']);
            unset($data['username']);
            unset($data['role']);

            if (isset($data['email']) && !empty($data['email'])) {
                $currentUser = $this->find($id);
                if ($currentUser['email'] !== $data['email']) {
                    $existingUser = $this->where('email', $data['email'])->get();
                    if (!empty($existingUser)) {
                        $this->errors['email'] = 'Email này đã được sử dụng bởi tài khoản khác';
                        return false;
                    }
                }
            }

            $rules = array_intersect_key($this->rules, $data);
            if (!$this->validate($data, $rules)) {
                return false;
            }

            if (empty($data['birth_date'])) {
                $data['birth_date'] = null;
            }

            return $this->update($id, $data);
        } catch (\Exception $e) {

            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // Đổi mật khẩu
    public function changePassword($id, $currentPassword, $newPassword)
    {
        $user = $this->find($id);

        if (!password_verify($currentPassword, $user['password'])) {
            $this->errors['current_password'] = 'Mật khẩu hiện tại không đúng';
            return false;
        }

        if (strlen($newPassword) < 6) {
            $this->errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            return false;
        }

        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    // lịch sử đặt phòng
    public function getBookingHistory($userId)
    {
        $sql = "SELECT b.*, r.room_number, h.name as hotel_name 
                FROM bookings b 
                JOIN rooms r ON b.room_id = r.id 
                JOIN hotels h ON r.hotel_id = h.id 
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Danh sach khách sạn yêu thích
    public function getFavoriteHotels($userId)
    {
        $sql = "SELECT h.*, f.created_at as favorited_at
                FROM hotels h
                INNER JOIN favorites f ON h.id = f.hotel_id 
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Câp nhật avatar người dùng
    public function updateAvatar($id, $fileName)
    {
        try {
            $currentUser = $this->find($id);
            $currentAvatar = $currentUser['avatar'];

            $result = $this->update($id, ['avatar' => $fileName]);

            if ($result && $currentAvatar != 'default.jpg') {
                $avatarPath = ROOT_PATH . '/public/images/avatars/' . $currentAvatar;
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                }
            }
            return $result;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // Kiểm tra khách sạn đã yêu thích chưa
    public function isFavorite($userId, $hotelId)
    {
        $sql = "SELECT COUNT(*) AS count FROM favorites 
                WHERE user_id = ? AND hotel_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $hotelId]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    // Thêm khách sạn vào danh sách yêu thích
    public function addFavorite($userId, $hotelId)
    {
        try {
            if ($this->isFavorite($userId, $hotelId)) {
                return true;
            }

            $sql = "INSERT INTO favorites (user_id, hotel_id, created_at) 
                VALUES (?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $hotelId]);
        } catch (\PDOException $e) {

            return false;
        }
    }

    // Xóa khách sạn khỏi danh sách yêu thích
    public function removeFavoriteHotel($userId, $hotelId)
    {
        try {
            $sql = "DELETE FROM favorites WHERE user_id = ? AND hotel_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $hotelId]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {

            return false;
        }
    }

    public function toggleFavoriteHotel($userId, $hotelId)
    {
        try {
            // Kiểm tra khách sạn đã có trong danh sách yêu thích chưa
            $sql = "SELECT COUNT(*) AS count FROM favorites WHERE user_id = ? AND hotel_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $hotelId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                $sql = "DELETE FROM favorites WHERE user_id = ? AND hotel_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $hotelId]);
                return ['success' => true, 'is_favorite' => false];
            } else {
                $sql = "INSERT INTO favorites (user_id, hotel_id, created_at) VALUES (?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $hotelId]);
                return ['success' => true, 'is_favorite' => true];
            }
        } catch (\PDOException $e) {

            throw new \Exception("Không thể thực hiện thao tác này");
        }
    }

    // Lấy danh sách đánh giá của người dùng
    public function getReviews($userId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT r.*, h.name as hotel_name, h.image as hotel_image 
                FROM reviews r
                JOIN hotels h ON r.hotel_id = h.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    // Lấy chi tiết đánh giá theo ID
    public function getReviewById($reviewId)
    {
        $sql = "SELECT r.*, h.name as hotel_name, h.image as hotel_image
                FROM reviews r
                JOIN hotels h ON r.hotel_id = h.id
                WHERE r.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reviewId]);
        return $stmt->fetch();
    }

    // Kiểm tra người dùng có thể đánh giá
    public function canReview($userId, $hotelId)
    {
        $sql = "SELECT COUNT(*) AS count
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ? AND r.hotel_id = ? AND b.booking_status = 'completed'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $hotelId]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    // Lấy danh sách đặt phòng theo khoảng thời gian
    public function getBookingsByDateRange($userId, $startDate, $endDate, $limit = 10, $offset = 0)
    {
        $sql = "SELECT b.*, r.room_number, r.name as room_name, 
                       h.name as hotel_name, h.image as hotel_image
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ?
                AND ((b.check_in_date BETWEEN ? AND ?) 
                    OR (b.check_out_date BETWEEN ? AND ?))
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $userId,
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $limit,
            $offset
        ]);

        return $stmt->fetchAll();
    }

    // Huỷ booking
    public function cancelBooking($userId, $bookingId)
    {
        // Kiểm tra booking có thuộc về người dùng này không
        $sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $this->errors[] = 'Không tìm thấy đặt phòng hoặc bạn không có quyền hủy';
            return false;
        }

        if (!in_array($booking['booking_status'], ['pending', 'confirmed'])) {
            $this->errors[] = 'Không thể hủy đặt phòng ở trạng thái hiện tại';
            return false;
        }

        $sql = "UPDATE bookings 
                SET booking_status = 'cancelled', updated_at = NOW()
                WHERE id = ? AND user_id = ?";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$bookingId, $userId]);

        if (!$result) {
            $this->errors[] = 'Không thể hủy đặt phòng';
        }

        return $result;
    }

    // Kiểm tra người dùng đã đặt phòng tại khách sạn chưa
    public function hasBookedHotel($userId, $hotelId)
    {
        $sql = "SELECT COUNT(*) as count
                FROM bookings b
                JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = ? AND r.hotel_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $hotelId]);
        $result = $stmt->fetch();

        return ($result['count'] > 0);
    }
}
