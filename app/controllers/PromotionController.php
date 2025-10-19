<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Promotion;
use app\Models\Hotel;

class PromotionController extends BaseController
{
    private $promotion, $hotel;

    public function __construct()
    {
        parent::__construct();
        $this->promotion = new Promotion();
        $this->hotel = new Hotel();
    }

    // Hiển thị danh sách ưu đãi
    public function index()
    {
        $promotions = $this->promotion->getAll();

        $groupedPromotions = [
            'instant' => [],
            'verification' => [],
            'percentage' => [],
            'fixed' => []
        ];

        foreach ($promotions as $promo) {
            if (isset($promo['need_verification']) && $promo['need_verification'] == 1) {
                $groupedPromotions['verification'][] = $promo;
            } else {
                $groupedPromotions['instant'][] = $promo;
            }
            $groupedPromotions[$promo['discount_type']][] = $promo;
        }

        return $this->view('promotions/index', [
            'pageTitle' => 'Danh sách ưu đãi',
            'promotions' => $promotions,
            'groupedPromotions' => $groupedPromotions
        ]);
    }

    // Hiển thị chi tiết ưu đãi
    public function show($id)
    {
        $promotion = $this->promotion->find($id);
        // Lấy danh sách khách sạn áp dụng ưu đãi này
        $appliedHotels = $this->promotion->getAppliedHotels($id);

        // Thêm thông tin bổ sung cho mỗi khách sạn
        foreach ($appliedHotels as &$hotel) {
            $hotel['amenities'] = $this->hotel->getHotelAmenities($hotel['id']);
            $hotel['promotions'] = $this->hotel->getActivePromotions($hotel['id']);
            if (!isset($hotel['min_price'])) {
                $rooms = $this->hotel->getAvailableRooms($hotel['id'], date('Y-m-d'), date('Y-m-d', strtotime('+1 day')), 2, null);
                if (!empty($rooms)) {
                    $prices = array_column($rooms, 'price');
                    $hotel['min_price'] = !empty($prices) ? min($prices) : 0;
                } else {
                    $hotel['min_price'] = 0;
                }
            }

            if (!isset($hotel['avg_rating'])) {
                $hotel['avg_rating'] = $this->hotel->getAverageRating($hotel['id']);
            }
        }

        $favorites = [];
        if (isset($_SESSION['user_id'])) {
            $userFavorites = $this->hotel->getFavoritesByUser($_SESSION['user_id']);
            foreach ($userFavorites as $fav) {
                $favorites[$fav['id']] = true;
            }
        }

        return $this->view('promotions/show', [
            'pageTitle' => $promotion['title'],
            'promotion' => $promotion,
            'hotels' => $appliedHotels,
            'favorites' => $favorites
        ]);
    }
}
