<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Hotel;
use app\Models\Promotion;
use app\Models\Destination;

class HomeController extends BaseController
{
    private $hotel;
    private $promotion;
    private $destination;

    public function __construct()
    {
        parent::__construct();
        $this->hotel = new Hotel();
        $this->promotion = new Promotion();
        $this->destination = new Destination();
    }

    public function index()
    {
        try {
            $filters = [
                'city' => $this->getQuery('city'),
                'check_in' => $this->getQuery('check_in', date('Y-m-d', strtotime('+1 day'))),
                'check_out' => $this->getQuery('check_out', date('Y-m-d', strtotime('+2 days'))),
                'guests' => $this->getQuery('guests', 2),
                'star_rating' => $this->getQuery('star_rating'),
                'price_min' => $this->getQuery('price_min'),
                'price_max' => $this->getQuery('price_max'),
            ];

            $featuredHotels = [];
            $hotels = [];
            $popularCities = [];
            $vietnameseDestinations = [];
            $foreignDestinations = [];
            $promotions = [];
            $featuredDestinationsWithHotels = [];

            // Lấy thông tin sắp xếp
            $sort = [
                'field' => $this->getQuery('sort_by', 'star_rating'),
                'direction' => $this->getQuery('sort_dir', 'DESC')
            ];

            $featuredHotels = $this->hotel->getFeaturedHotels();

            $hotels = $this->hotel->search($filters, $sort);

            $popularCities = $this->getPopularDestinations();

            $vietnameseDestinations = $this->getVietnameseDestinations(3);

            $foreignDestinations = $this->getForeignDestinations(3);

            $promotions = $this->getPromotions();

            $featuredDestinationsWithHotels = $this->getFeaturedDestinationsWithHotels(4);

            $socialMedia = [];
            $socialMediaDir = ROOT_PATH . '/public/images/social-media/';
            if (is_dir($socialMediaDir)) {
                $files = scandir($socialMediaDir);
                foreach ($files as $file) {
                    // Bỏ qua . và .. và chỉ lấy các file hình ảnh
                    if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                        $socialMedia[] = SITE_URL . '/public/images/social-media/' . $file;
                    }
                }
            } else {
            }

            return $this->view('home/index', [
                'featuredHotels' => $featuredHotels,
                'hotels' => $hotels,
                'popularCities' => $popularCities,
                'promotions' => $promotions,
                'filters' => $filters,
                'sort' => $sort,
                'pageTitle' => 'Trang chủ',
                'vietnameseDestinations' => $vietnameseDestinations,
                'foreignDestinations' => $foreignDestinations, // Add this new variable
                'featuredDestinationsWithHotels' => $featuredDestinationsWithHotels,
                'socialMedia' => $socialMedia,
            ]);
        } catch (\Exception $e) {
        }
    }

    // Lấy dánh sách khuyến mãi của nền tảng
    private function getPromotions()
    {
        try {
            return $this->promotion->getAll();
        } catch (\Exception $e) {
        }
    }

    // lấy danh sách điểm đến phổ biến
    private function getPopularDestinations($limit = 3)
    {
        try {
            $destinations = $this->destination->getPopularDestinations($limit);

            if (empty($destinations)) {

                throw new \Exception('No destinations found from primary source');
            }

            foreach ($destinations as &$destination) {
                $destination['city'] = $destination['name'];
                if (empty($destination['image'])) {
                    $destination['image'] = SITE_URL . '/public/images/cities/default.jpg';
                } else {
                    if (strpos($destination['image'], 'http') !== 0 && strpos($destination['image'], SITE_URL) !== 0) {
                        $destination['image'] = SITE_URL . '/public/images/cities/' . $destination['image'];
                    }
                }

                if (!isset($destination['hotel_count'])) {
                    $destination['hotel_count'] = 0;
                }
            }

            return $destinations;
        } catch (\Exception $e) {


            try {
                $cities = $this->hotel->getPopularCities($limit);
                if (!empty($cities)) {

                    return $cities;
                }

                throw new \Exception('Fallback 1 failed: No cities found');
            } catch (\Exception $e) {
            }
        }
    }


    // Lấy danh sách điểm đến ở Việt Nam
    private function getVietnameseDestinations($limit = 6)
    {
        try {
            $destinations = $this->destination->getDestinationsByRegion('Việt Nam', $limit);


            foreach ($destinations as &$destination) {
                if (empty($destination['image'])) {
                    $destination['image'] = SITE_URL . '/public/images/cities/default.jpg';
                } else {
                    if (strpos($destination['image'], 'http') !== 0 && strpos($destination['image'], SITE_URL) !== 0) {
                        $destination['image'] = SITE_URL . '/public/images/cities/' . $destination['image'];
                    }
                }

                if (!isset($destination['hotel_count'])) {
                    $destination['hotel_count'] = 0;
                }
            }

            return $destinations;
        } catch (\Exception $e) {

            return [];
        }
    }

    // Lấy danh sách điểm đến nổi bật kèm khách sạn
    private function getFeaturedDestinationsWithHotels()
    {
        try {
            // Lấy 5 điểm đến đầu tiên sắp xếp theo ID
            $destinations = $this->destination->getDestinationsSortedById(5);

            if (empty($destinations)) {

                return [];
            }

            // Cho mỗi điểm đến, lấy tối đa 4 khách sạn
            foreach ($destinations as &$destination) {
                $hotels = $this->hotel->getHotelsByDestination($destination['id']);
                foreach ($hotels as &$hotel) {
                    $hotel['image'] = SITE_URL . '/public/images/hotels/' . $hotel['id'] . '/main.jpg';
                }
                $destination['hotels'] = $hotels;
            }

            return $destinations;
        } catch (\Exception $e) {

            return [];
        }
    }

    // Lấy danh sách điểm đến nước ngoài
    private function getForeignDestinations($limit = 3)
    {
        try {
            $destinations = $this->destination->getForeignDestinations($limit);

            if (empty($destinations)) {
                $destinations = $this->destination->getDestinationsExcludeRegion('Việt Nam', $limit);
            }


            foreach ($destinations as &$destination) {
                if (empty($destination['image'])) {
                    $destination['image'] = SITE_URL . '/public/images/cities/default.jpg';
                } else {
                    if (strpos($destination['image'], 'http') !== 0 && strpos($destination['image'], SITE_URL) !== 0) {
                        $destination['image'] = SITE_URL . '/public/images/cities/' . $destination['image'];
                    }
                }

                if (!isset($destination['hotel_count'])) {
                    $destination['hotel_count'] = 0;
                }

                if (!isset($destination['country_name']) && isset($destination['country'])) {
                    $destination['country_name'] = $destination['country'];
                }
            }

            return $destinations;
        } catch (\Exception $e) {

            return [];
        }
    }
    // xử lý liên hệ từ người dùng
    public function submitContact()
    {
        if (!$this->isPost()) {
            return $this->redirect('/contact');
        }

        if (!$this->validateCsrfToken()) {
            $this->setFlash('error', 'Yêu cầu không hợp lệ, vui lòng thử lại.');
            return $this->redirect('/contact');
        }

        $data = $this->getAllPost();

        $errors = [];
        if (empty($data['fullname'])) {
            $errors[] = 'Vui lòng nhập họ tên';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Vui lòng nhập email hợp lệ';
        }
        if (empty($data['message'])) {
            $errors[] = 'Vui lòng nhập nội dung tin nhắn';
        }

        if (!empty($errors)) {
            $this->setFlash('error', reset($errors));
            return $this->redirect('/contact');
        }
        $this->setFlash('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể!');
        return $this->redirect('/contact');
    }
}
