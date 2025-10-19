<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Hotel;
use app\Models\Destination;

class SearchController extends BaseController
{
    private $hotel;
    private $destination;

    public function __construct()
    {
        parent::__construct();
        $this->hotel = new Hotel();
        $this->destination = new Destination();
    }

    // Hiển thị trang tìm kiếm khách sạn
    public function index()
    {
        // Lấy tất cả các tham số tìm kiếm từ URL
        $filters = [
            'city' => $this->getQuery('city'),
            'check_in' => $this->getQuery('check_in', date('Y-m-d', strtotime('+1 day'))),
            'check_out' => $this->getQuery('check_out', date('Y-m-d', strtotime('+2 days'))),
            'guests' => (int)$this->getQuery('guests', 2),
            'avg_rating' => $this->getQuery('avg_rating'),
            'price_min' => $this->getQuery('price_min'),
            'price_max' => $this->getQuery('price_max'),
            'limit' => 10,
        ];


        $sort = [
            'field' => $this->getQuery('sort_by', 'min_price'),
            'direction' => $this->getQuery('sort_dir', 'DESC')
        ];

        $hotels = $this->hotel->search($filters, $sort);
        foreach ($hotels as &$hotel) {
            $allPromotions = $this->hotel->getActivePromotions($hotel['id']);
            $hotel['total_promotions'] = count($allPromotions);
            $hotel['promotions'] = array_slice($allPromotions, 0, 3); // Chỉ lấy 3 khuyến mãi
        }

        $destinations = $this->destination->getAllDestinations();

        $favorites = [];
        if (isset($_SESSION['user_id'])) {
            $userFavorites = $this->hotel->getFavoritesByUser($_SESSION['user_id']);
            foreach ($userFavorites as $fav) {
                $favorites[$fav['id']] = true;
            }
        }

        return $this->view('search/index', [
            'hotels' => $hotels,
            'filters' => $filters,
            'sort' => $sort,
            'favorites' => $favorites,
            'destinations' => $destinations,
            'pageTitle' => 'Tìm kiếm khách sạn' . (!empty($filters['city']) ? ' - ' . $filters['city'] : '')
        ]);
    }
}
