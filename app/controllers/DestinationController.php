<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Destination;
use app\Models\Hotel;
use app\Models\Promotion;

class DestinationController extends BaseController
{
    private $destinationModel;
    private $hotelModel;

    public function __construct()
    {
        parent::__construct();
        $this->destinationModel = new Destination();
        $this->hotelModel = new Hotel();
    }

    // Hiển thị trang điểm đến du lịch phổ biến
    public function byRegion($region)
    {
        $region = urldecode($region);

        $destinations = $this->destinationModel->getDestinationsByRegion($region, 1000);

        $allRegions = $this->getAllRegions();

        return $this->view('destination/region', [
            'pageTitle' => 'Khám phá ' . $region,
            'currentRegion' => $region,
            'destinations' => $destinations,
            'allRegions' => $allRegions
        ]);
    }


    private function getAllRegions()
    {
        $allDestinations = $this->destinationModel->getAllDestinations();
        return array_values(array_unique(array_column($allDestinations, 'region')));
    }

    // Hiển thị khách sạn tại một thành phố cụ thể
    public function cityHotels($cityName)
    {
        // Giải mã tên thành phố từ URL
        $cityName = urldecode($cityName);

        $filters = [
            'city' => $cityName,
            'check_in' => $this->getQuery('check_in', date('Y-m-d', strtotime('+1 day'))),
            'check_out' => $this->getQuery('check_out', date('Y-m-d', strtotime('+2 days'))),
            'guests' => (int)$this->getQuery('guests', 2),
            'avg_rating' => $this->getQuery('avg_rating', ''), // Đặt giá trị mặc định là chuỗi rỗng
            'price_min' => is_numeric($this->getQuery('price_min')) ? (int)$this->getQuery('price_min') : null,
            'price_max' => is_numeric($this->getQuery('price_max')) ? (int)$this->getQuery('price_max') : null,
            'amenities' => $this->getQuery('amenities', []) // Thêm để hỗ trợ lọc theo tiện ích
        ];

        if (!empty($filters['avg_rating'])) {
            $filters['avg_rating'] = (int)$filters['avg_rating'];
        }

        $sort = [
            'field' => $this->getQuery('sort_by', 'avg_rating'),
            'direction' => $this->getQuery('sort_dir', 'DESC')
        ];

        $hotels = $this->hotelModel->search($filters, $sort);

        if (!is_array($hotels)) {
            $hotels = [];
        }

        foreach ($hotels as &$hotel) {
            $allPromotions = $this->hotelModel->getActivePromotions($hotel['id']);
            $hotel['total_promotions'] = count($allPromotions);
            $hotel['promotions'] = array_slice($allPromotions, 0, 3); // Chỉ lấy 3 khuyến mãi
        }

        $favorites = [];
        if (isset($_SESSION['user_id'])) {
            $userFavorites = $this->hotelModel->getFavoritesByUser($_SESSION['user_id']);
            foreach ($userFavorites as $fav) {
                $favorites[$fav['id']] = true;
            }
        }

        return $this->view('destination/city_hotels', [
            'pageTitle' => 'Khách sạn tại ' . $cityName,
            'cityName' => $cityName,
            'hotels' => $hotels,
            'filters' => $filters,
            'sort' => $sort,
            'favorites' => $favorites
        ]);
    }
}
