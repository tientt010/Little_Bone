<?php

namespace app\Controllers;

use app\Models\Hotel;
use app\Models\Destination;
use app\Models\Amenity;
use app\Models\Promotion;
use app\Models\Room;


class HotelStaffController extends HotelController
{
    private $hotel;
    private $destination;
    private $amenity;
    private $promotion;
    private $room;

    public function __construct()
    {
        parent::__construct();
        $this->checkAccess();
        $this->destination = new Destination();
        $this->amenity = new Amenity();
        $this->promotion = new Promotion();
        $this->hotel = new Hotel();
        $this->room = new Room();
    }

    // Kiểm tra quyền truy cập trước khi thực hiện bất kỳ hành động nào
    private function checkAccess()
    {
        // Kiểm tra người dùng đã đăng nhập và có quyền hotel_staff
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'hotel_staff') {
            $this->setFlash('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect('/login');
            exit;
        }
    }

    /**
     * Hiển thị dashboard cho nhân viên khách sạn
     */
    public function dashboard()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $hotelId = $_SESSION['hotel_id'] ?? null;

        if (!isset($hotelId) || !$this->hotel->checkStaffAccess($userId, $hotelId)) {
            $this->setFlash('error', 'Bạn không có quyền truy cập vào trang này.');
            $this->redirect('/login');
            return;
        }

        $hotelInfo = $this->hotel->find($hotelId, ['name', 'status']);
        $today = date('Y-m-d');
        $lastYear = date('Y-m-d', strtotime('-1 year'));
        $otherYear = date('Y-m-d', strtotime('-2 year'));

        $prevBookings = $this->hotel->countBookingItems($hotelId, $otherYear, $lastYear);
        if ($prevBookings === 0) {
            $prevBookings = 1;
        }
        $currBookings = $this->hotel->countBookingItems($hotelId, $lastYear, $today);

        $prevRevenue = $this->hotel->getRevenue($hotelId, $otherYear, $lastYear);
        if ($prevRevenue === 0) {
            $prevRevenue = 1;
        }
        $currRevenue = $this->hotel->getRevenue($hotelId, $lastYear, $today);
        $preFillRate = $this->hotel->getFillRate($hotelId, $otherYear, $lastYear);
        if ($preFillRate === 0) {
            $preFillRate = 1;
        }

        $currFillRate = $this->hotel->getFillRate($hotelId, $lastYear, $today);

        $preAvgRating = $this->hotel->getAverageRating($hotelId, $otherYear, $lastYear);
        if ($preAvgRating === 0) {
            $preAvgRating = 1;
        }
        $currAvgRating = $this->hotel->getAverageRating($hotelId, $lastYear, $today);

        $startDate = date('Y-01-01');

        $charts = $this->getCharts($startDate, 12, 'year', $hotelId);

        $recentBookings = $this->hotel->getRecentBookings($hotelId);
        $this->view('hotel_staff/dashboard', [
            'hotel' => $hotelInfo,
            'stats' => [
                'total_bookings' => $currBookings,
                'booking_trend' => round(($currBookings - $prevBookings) / $prevBookings, 2) * 100,
                'total_revenue' => $currRevenue,
                'revenue_trend' => round(($currRevenue - $prevRevenue) / $prevRevenue, 2) * 100,
                'occupancy_rate' => $currFillRate,
                'occupancy_trend' => round(($currFillRate - $preFillRate) / $preFillRate, 2) * 100,
                'avg_rating' => $currAvgRating,
                'rating_trend' => $currAvgRating - $preAvgRating
            ],
            'charts' => $charts,
            'recent_bookings' => $recentBookings,
        ]);
    }

    // Quản lý khách sạn
    public function manage_hotel($hotelId = null)
    {
        if ($hotelId === null && isset($_SESSION['hotel_id'])) {
            $hotelId = $_SESSION['hotel_id'];
        }

        if (!$hotelId) {
            $this->setFlash('error', 'Không tìm thấy thông tin khách sạn.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }

        $userId = $_SESSION['user_id'];
        if (!$this->hotel->checkStaffAccess($userId, $hotelId)) {
            $this->setFlash('error', 'Bạn không có quyền quản lý khách sạn này.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }

        $hotel = $this->hotel->getHotelById($hotelId);
        if (!$hotel) {
            $this->setFlash('error', 'Không tìm thấy thông tin khách sạn.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }
        $hotel['avg_rating'] = $this->hotel->getAverageRating($hotelId);

        $_SESSION['hotel_id'] = $hotelId;

        $this->view('hotel_staff/manage_hotel', [
            'hotel' => $hotel,
            'destinations' => $this->destination->getAll()
        ]);
    }

    // Cập nhật thông tin khách sạn
    public function updateHotel()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            }

            $hotelId = $_POST['hotel_id'] ?? null;
            if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền cập nhật thông tin khách sạn này.']);
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'address' => $_POST['address'] ?? '',
                'destination_id' => $_POST['destination_id'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            $this->hotel->update($hotelId, $data);
            return $this->jsonResponse(['success' => true, 'message' => 'Cập nhật thông tin khách sạn thành công.']);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật thông tin khách sạn.']);
        }
    }

    // Cập nhật vị trí khách sạn
    public function updateLocation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            return;
        }

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data || !isset($data['hotel_id']) || !isset($data['latitude']) || !isset($data['longitude'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        $hotelId = $data['hotel_id'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];

        if (!$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền cập nhật khách sạn này.']);
            return;
        }

        $result = $this->hotel->update($hotelId, ['latitude' => $latitude, 'longitude' => $longitude]);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Cập nhật vị trí thành công.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật vị trí.']);
        }
    }

    // Tải lên hình ảnh khách sạn
    public function uploadHotelPhotos()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            return;
        }

        $hotelId = $_POST['hotel_id'] ?? null;
        $filename = $_POST['filename'] ?? null;

        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền tải lên hình ảnh.']);
            return;
        }

        if (!isset($_FILES['photo'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy file.']);
            return;
        }

        $file = $_FILES['photo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['success' => false, 'message' => 'Lỗi khi tải file: ' . $this->getUploadErrorMessage($file['error'])]);
            return;
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $this->jsonResponse(['success' => false, 'message' => 'File tải lên không phải là ảnh hợp lệ.']);
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $this->jsonResponse(['success' => false, 'message' => 'Kích thước file không được vượt quá 2MB.']);
            return;
        }

        $uploadDir = ROOT_PATH . "/public/images/hotels/{$hotelId}/";

        if (!is_dir($uploadDir)) {

            if (!mkdir($uploadDir, 0755, true)) {
                $this->jsonResponse(['success' => false, 'message' => 'Không thể tạo thư mục lưu trữ' . $uploadDir]);
                return;
            }
        }

        $targetFilename = $filename ?: 'main.jpg';
        $uploadPath = $uploadDir . $targetFilename;

        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không thể lưu file ảnh. Vui lòng thử lại sau.']);
            return;
        }
        $imageUrl = SITE_URL . "/public/images/hotels/{$hotelId}/{$targetFilename}";
        $this->jsonResponse([
            'success' => true,
            'message' => 'Tải lên hình ảnh thành công.',
            'image_url' => $imageUrl,
            'filename' => $targetFilename,
            'timestamp' => time() // Thêm timestamp vào response để sử dụng ở client
        ]);
    }

    // lấy thông điệp lỗi upload file
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Kích thước file vượt quá giới hạn upload_max_filesize trong php.ini.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Kích thước file vượt quá giới hạn MAX_FILE_SIZE trong form HTML.';
            case UPLOAD_ERR_PARTIAL:
                return 'File chỉ được tải lên một phần.';
            case UPLOAD_ERR_NO_FILE:
                return 'Không có file nào được tải lên.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Thư mục tạm không tồn tại.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Không thể ghi file vào đĩa.';
            case UPLOAD_ERR_EXTENSION:
                return 'Tải file bị dừng bởi extension.';
            default:
                return 'Lỗi không xác định.';
        }
    }

    // Lấy danh sách tiện ích đã áp dụng cho khách sạn
    public function getAppliedAmenities($hotelId)
    {
        if (!$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
            return;
        }

        $amenities = $this->hotel->getHotelAmenities($hotelId);

        $this->jsonResponse(['success' => true, 'amenities' => $amenities]);
    }

    // Lấy danh sách tất cả tiện ích
    public function getAmenities()
    {
        $amenities = $this->amenity->getAll();
        $this->jsonResponse(['success' => true, 'amenities' => $amenities]);
    }

    // Cập nhật tiện ích cho khách sạn
    public function updateAmenities()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            return;
        }
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        $hotelId = $data['hotel_id'] ?? null;
        $amenityIds = $data['amenities'] ?? [];
        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền cập nhật tiện ích.']);
            return;
        }

        $result = $this->hotel->updateHotelAmenities($hotelId, $amenityIds);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Cập nhật tiện ích thành công.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật tiện ích.']);
        }
    }

    // Lấy danh sách khuyến mãi
    public function getPromotions()
    {
        $promotions = $this->promotion->getAll();

        $this->jsonResponse(['success' => true, 'promotions' => $promotions]);
    }

    // Lấy danh sách khuyến mãi đã áp dụng cho khách sạn
    public function getAppliedPromotions($hotelId)
    {
        if (!$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
            return;
        }
        $appliedPromotions = $this->promotion->getAppliedPromotions($hotelId);
        $this->jsonResponse(['success' => true, 'appliedPromotions' => $appliedPromotions]);
    }

    // Câp nhật khuyến mãi cho khách sạn
    public function updatePromotions()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            return;
        }
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        $hotelId = $data['hotel_id'] ?? null;
        $promotionIds = $data['promotions'] ?? [];

        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền cập nhật khuyến mãi.']);
            return;
        }

        $result = $this->promotion->updateHotelPromotions($hotelId, $promotionIds);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Cập nhật khuyến mãi thành công.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật khuyến mãi.']);
        }
    }

    // cập nhật trạng thái khách sạn
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            return;
        }

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data || !isset($data['hotel_id']) || !isset($data['status'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        $hotelId = $data['hotel_id'];
        $status = $data['status'];

        if (!$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Không có quyền cập nhật khách sạn này.']);
            return;
        }

        if ($status !== 'active' && $status !== 'inactive') {
            $this->jsonResponse(['success' => false, 'message' => 'Trạng thái không hợp lệ.']);
            return;
        }

        $result = $this->hotel->update($hotelId, ['status' => $status]);

        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Cập nhật trạng thái thành công.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật trạng thái.']);
        }
    }

    // Lấy thông tin thống kê phòng khách sạn
    public function getRoomStats()
    {
        if (isset($_SESSION['hotel_id'])) {
            $hotelId = $_SESSION['hotel_id'];
        } else {
        }

        if (!$hotelId) {
            $this->setFlash('error', 'Không tìm thấy thông tin khách sạn.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }

        $userId = $_SESSION['user_id'];
        if (!$this->hotel->checkStaffAccess($userId, $hotelId)) {
            $this->setFlash('error', 'Bạn không có quyền quản lý khách sạn này.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }
        $rooms = $this->hotel->getRoomsByHotelId($hotelId);

        $availableRooms = [];
        $bookedRooms = [];
        $maintenanceRooms = [];
        foreach ($rooms as $room) {
            if ($room['status'] === 'available') {
                $availableRooms[] = $room;
            } elseif ($room['status'] === 'booked') {
                $bookedRooms[] = $room;
            } elseif ($room['status'] === 'maintenance') {
                $maintenanceRooms[] = $room;
            }
        }
        return $this->jsonResponse([
            'success' => true,
            'stats' => [
                'total' => count($rooms),
                'available' => count($availableRooms),
                'booked' => count($bookedRooms),
                'maintenance' => count($maintenanceRooms)
            ],
            'message' => 'Lấy thông tin thống kê thành công.'
        ]);
    }

    // Quản lý phòng khách sạn
    public function manage_rooms()
    {
        if (isset($_SESSION['hotel_id'])) {
            $hotelId = $_SESSION['hotel_id'];
        } else {
        }

        if (!$hotelId) {
            $this->setFlash('error', 'Không tìm thấy thông tin khách sạn.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }

        $userId = $_SESSION['user_id'];
        if (!$this->hotel->checkStaffAccess($userId, $hotelId)) {
            $this->setFlash('error', 'Bạn không có quyền quản lý khách sạn này.');
            $this->redirect('/hotel_staff/dashboard');
            return;
        }
        $rooms = $this->hotel->getRoomsByHotelId($hotelId);

        $availableRooms = [];
        $bookedRooms = [];
        $maintenanceRooms = [];
        foreach ($rooms as $room) {
            if ($room['status'] === 'available') {
                $availableRooms[] = $room;
            } elseif ($room['status'] === 'booked') {
                $bookedRooms[] = $room;
            } elseif ($room['status'] === 'maintenance') {
                $maintenanceRooms[] = $room;
            }
        }
        $this->view('hotel_staff/manage_rooms', [
            'stats' => [
                'total' => count($rooms),
                'available' => count($availableRooms),
                'booked' => count($bookedRooms),
                'maintenance' => count($maintenanceRooms)
            ],
            'rooms' => $rooms,
        ]);
    }

    // Thêm phòng mới
    public function addRoom()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/hotel_staff/manage_rooms');
                return;
            }

            $hotelId = $_SESSION['hotel_id'] ?? null;
            if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền thêm phòng cho khách sạn này.']);
            }
            $data = [
                'hotel_id' => $hotelId,
                'room_number' => $_POST['room_number'] ?? '',
                'name' => $_POST['name'] ?? '',
                'room_type' => $_POST['room_type'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'capacity' => $_POST['capacity'] ?? 1,
                'status' => $_POST['status'] ?? 'available',
                'area' => $_POST['area'] ?? 0,
                'description' => $_POST['description'] ?? ''
            ];
            $roomId = $this->room->create($data);
            if ($roomId) {
                // Xử lý hình ảnh nếu có
                if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = ROOT_PATH . "/public/images/rooms/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $uploadPath = $uploadDir . $roomId . ".jpg";
                    move_uploaded_file($_FILES['room_image']['tmp_name'], $uploadPath);
                } else if (!empty($_POST['room_image']) && is_string($_POST['room_image'])) {
                    $image = $_POST['room_image'];
                    if (strpos($image, 'data:image/') === 0) {
                        $uploadDir = ROOT_PATH . "/public/images/rooms/";
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $uploadPath = $uploadDir . $roomId . ".jpg";
                        $image = explode(',', $image)[1] ?? $image;
                        $decodedImage = base64_decode($image);

                        if ($decodedImage !== false) {
                            file_put_contents($uploadPath, $decodedImage);
                        }
                    }
                }

                $newRoom = $this->room->find($roomId);
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Thêm phòng thành công.',
                    'room' => array_merge($newRoom)
                ]);
            } else {
                $errors = $this->room->getErrors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Có lỗi xảy ra khi thêm phòng.';
                return $this->jsonResponse(['success' => false, 'message' => $errorMessage]);
            }
        } catch (\Exception $e) {

            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm phòng2.']);
        }
    }

    // Xóa phòng
    public function deleteRoom($roomId = null)
    {
        $hotelId = $_SESSION['hotel_id'] ?? null;
        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
        }

        if ($roomId === null) {
            $roomId = $_POST['room_id'] ?? null;
        }

        if (!$roomId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy thông tin phòng.']);
        }

        $room = $this->room->find($roomId);
        if (!$room || $room['hotel_id'] != $hotelId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Phòng không thuộc khách sạn này.']);
        }
        $imagePath = ROOT_PATH . "/public/images/rooms/{$roomId}.jpg";
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $this->room->delete($roomId);
        $this->redirect('/hotel_staff/manage_rooms');
    }

    // lọc phòng theo loại, trạng thái và khoảng giá
    public function filterRooms()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
        }

        $hotelId = $_SESSION['hotel_id'] ?? null;
        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
        }
        $filter = [
            'room_type' => $_POST['type'] ?? '',
            'status' => $_POST['status'] ?? '',
        ];

        $priceRange = $_POST['price'] ?? '';
        if ($priceRange) {
            $priceParts = explode('-', $priceRange);
            if (count($priceParts) === 2) {
                $filter['min_price'] = (float)$priceParts[0];
                $filter['max_price'] = (float)$priceParts[1];
            }
        }

        $rooms = $this->room->search($hotelId, $filter);


        return $this->jsonResponse([
            'success' => true,
            'message' => 'Lọc phòng thành công.',
            'rooms' => $rooms
        ]);
    }

    // Lấy thông tin chi tiết của một phòng
    public function getRoomDetails($roomId)
    {
        $hotelId = $_SESSION['hotel_id'] ?? null;
        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
        }
        $room = $this->room->getDetailRoom($roomId);
        if ($room) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Lấy thông tin phòng thành công.',
                'room' => $room
            ]);
        } else {
            return $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy thông tin phòng.']);
        }
    }

    public function editRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
        }

        $hotelId = $_SESSION['hotel_id'] ?? null;
        if (!$hotelId || !$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không có quyền truy cập.']);
        }
        try {
            $data = [
                'room_number' => $_POST['room_number'] ?? '',
                'name' => $_POST['name'] ?? '',
                'room_type' => $_POST['room_type'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'capacity' => $_POST['capacity'] ?? 1,
                'status' => $_POST['status'] ?? 'available',
                'area' => $_POST['area'] ?? 0,
                'description' => $_POST['description'] ?? ''
            ];
            $roomId = $_POST['room_id'] ?? null;
            if (!$roomId) {
                return $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy thông tin phòng.']);
            }

            $imageUploaded = false;

            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . "/public/images/rooms/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $uploadPath = $uploadDir . $roomId . ".jpg";
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $uploadPath)) {
                    $imageUploaded = true;
                }
            }
            // Xử lý base64 nếu không có file upload
            else if (!empty($_POST['room_image']) && is_string($_POST['room_image'])) {
                $image = $_POST['room_image'];
                if (strpos($image, 'data:image/') === 0) {
                    $uploadDir = ROOT_PATH . "/public/images/rooms/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $uploadPath = $uploadDir . $roomId . ".jpg";
                    $image = explode(',', $image)[1] ?? $image;
                    $decodedImage = base64_decode($image);

                    if ($decodedImage !== false) {
                        file_put_contents($uploadPath, $decodedImage);
                        $imageUploaded = true;
                    }
                }
            }

            $result = $this->room->update($roomId, $data);

            $updatedRoom = $this->room->getDetailRoom($roomId);

            if ($result) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Cập nhật phòng thành công' . ($imageUploaded ? ' với hình ảnh mới' : ''),
                    'room' => $updatedRoom
                ]);
            } else {
                $errors = $this->room->getErrors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Có lỗi xảy ra khi cập nhật phòng.';
                return $this->jsonResponse(['success' => false, 'message' => $errorMessage]);
            }
        } catch (\Exception $e) {

            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật phòng.']);
        }
    }

    // Lấy ngày trong tuần bằng tiếng Việt
    private function getDayOfWeekInVietnamese($date)
    {
        $dayNumber = (int)$date->format('N');

        $vietnameseDays = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ Nhật'
        ];

        return $vietnameseDays[$dayNumber];
    }


    // Tạo dữ liệu thống kê cho biểu đồ
    public function getCharts($startDate, $labelCount, $statsType, $hotelId = null)
    {
        try {
            if ($hotelId === null) {
                $hotelId = $_SESSION['hotel_id'] ?? null;
            }

            if (!$this->hotel->checkStaffAccess($_SESSION['user_id'], $hotelId)) {
                return ['categories' => [], 'values' => []];
            }

            try {
                $startDate = new \DateTime($startDate);
            } catch (\Exception $e) {

                return ['categories' => [], 'values' => []];
            }


            $charts = [
                'revenue' => ['categories' => [], 'values' => []],
                'occupancy' => ['categories' => [], 'values' => []],
                'ratings' => ['categories' => [], 'values' => []],
                'room_types' => ['categories' => [], 'values' => []]
            ];
            $currentDate = clone $startDate;
            $endDate = clone $startDate;
            for ($i = 0; $i < $labelCount; $i++) {
                $endDate = clone $currentDate;
                if ($statsType == 'week') {
                } else if ($statsType == 'month') {
                    $endDate->modify('2 days');
                } else if ($statsType == 'year') {
                    $endDate->modify('last day of this month');
                }
                $revenue = $this->hotel->getRevenue(
                    $hotelId,
                    $currentDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                );
                $occupancy = $this->hotel->getFillRate(
                    $hotelId,
                    $currentDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                );
                $ratings = $this->hotel->getAverageRating(
                    $hotelId,
                    $currentDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                );
                $categories = $currentDate->format('d/m/Y');
                if ($statsType == 'week') {
                    $categories = $this->getDayOfWeekInVietnamese($currentDate);
                } else if ($statsType == 'month') {
                    $categories = $currentDate->format('d');
                } else if ($statsType == 'year') {
                    $categories = $currentDate->format('m/y');
                }
                $charts['revenue']['categories'][] = $categories;
                $charts['occupancy']['categories'][] = $categories;
                $charts['ratings']['categories'][] = $categories;
                $charts['revenue']['values'][] = $revenue;
                $charts['occupancy']['values'][] = $occupancy;
                $charts['ratings']['values'][] = $ratings;

                $currentDate = $endDate;
                $currentDate->modify('+1 day');
            }
            $startDateStr = $startDate->format('Y-m-d');
            $endDateStr = $endDate->format('Y-m-d');

            $totalBookings = $this->hotel->countBookingItems($hotelId, $startDateStr, $endDateStr);
            if ($totalBookings == 0) {
                $totalBookings = 1;
            }


            $charts['room_types']['categories'] = ['single', 'double', 'suite', 'family'];
            $charts['room_types']['values'] = [
                round($this->hotel->roomTypeBookings($hotelId, $startDateStr, $endDateStr, 'single') / $totalBookings * 100, 2),
                round($this->hotel->roomTypeBookings($hotelId, $startDateStr, $endDateStr, 'double') / $totalBookings * 100, 2),
                round($this->hotel->roomTypeBookings($hotelId, $startDateStr, $endDateStr, 'suite') / $totalBookings * 100, 2),
                round($this->hotel->roomTypeBookings($hotelId, $startDateStr, $endDateStr, 'family') / $totalBookings * 100, 2),
            ];

            return $charts;
        } catch (\Exception $e) {

            return ['categories' => [], 'values' => []];
        }
    }

    // Lấy dữ liệu thống kê cho dashboard bằng AJAX
    public function getDashboardData()
    {
        $hotelId = $_SESSION['hotel_id'] ?? null;
        if (!$hotelId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy thông tin khách sạn']);
        }

        $startDate = $_POST['start_date'] ?? date('d/m/Y', strtotime('-30 days'));
        $statsType = $_POST['stats_type'] ?? 'day';
        $labelCount = (int)($_POST['label_count'] ?? 7);

        $charts = $this->getCharts($startDate, $labelCount, $statsType, $hotelId);


        return $this->jsonResponse([
            'success' => true,
            'charts' => $charts,
            'message' => 'Lấy dữ liệu thành công'
        ]);
    }
}
