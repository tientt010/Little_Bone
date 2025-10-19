<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Destination;
use app\Models\Hotel;
use app\Models\Promotion;
use app\Models\User;

class ApiController extends BaseController
{
    private $destinationModel;
    private $hotelModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->destinationModel = new Destination();
        $this->hotelModel = new Hotel();
        $this->userModel = new User();
    }

    // Lấy danh sách địa điểm du lịch phổ biến
    public function searchDestinations()
    {
        $query = $this->getQuery('q', '');

        if (strlen($query) < 2) {
            $destinations = $this->destinationModel->getPopularDestinations(10);
        } else {
            $destinations = $this->destinationModel->searchDestinations(['name' => $query]);
        }

        foreach ($destinations as &$destination) {
            if (!isset($destination['hotel_count'])) {
                $destination['hotel_count'] = $this->destinationModel->countHotels($destination['id']);
            }
            $destination['hotel_count_text'] = $destination['hotel_count'] . ' khách sạn';
        }

        $this->sendJsonResponse(['destinations' => $destinations]);
    }

    // thêm hoặc xóa khách sạn khỏi danh sách yêu thích
    public function toggleFavorite()
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
            $result = $this->userModel->toggleFavoriteHotel($userId, $hotelId);

            return $this->jsonResponse([
                'success' => true,
                'is_favorite' => $result['is_favorite'],
                'message' => $result['is_favorite'] ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích'
            ]);
        } catch (\Exception $e) {

            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xử lý yêu cầu']);
        }
    }

    public function getHotelPromotions($hotelId)
    {
        try {
            if (!is_numeric($hotelId)) {
                throw new \Exception("ID khách sạn không hợp lệ");
            }

            $hotelId = (int)$hotelId;

            $promotions = $this->hotelModel->getActivePromotions($hotelId);

            $this->sendJsonResponse([
                'success' => true,
                'hotel_id' => $hotelId,
                'promotions' => $promotions
            ]);
        } catch (\Exception $e) {

            $this->sendJsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
