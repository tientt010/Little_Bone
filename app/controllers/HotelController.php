<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Hotel;

class HotelController extends BaseController
{
    private $hotel;

    public function __construct()
    {
        parent::__construct();
        $this->hotel = new Hotel();
    }

    // Hiển thị chi tiết khách sạn
    public function show($id, $slug = null)
    {
        $hotel = $this->hotel->getDetails($id);

        if (!$hotel) {
            $this->setFlash('error', 'Không tìm thấy khách sạn');
            return $this->redirect('/');
        }
        $correctSlug = $this->createSlug($hotel['name']);

        if ($slug !== $correctSlug) {
            $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

            $redirectUrl = "/hotels/{$id}/{$correctSlug}" . $queryString;

            return $this->redirect($redirectUrl);
        }

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $checkIn = filter_input(INPUT_GET, 'check_in', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $checkOut = filter_input(INPUT_GET, 'check_out', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $guests = filter_input(INPUT_GET, 'guests', FILTER_VALIDATE_INT) ?: 2;
        $roomType = filter_input(INPUT_GET, 'room_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;

        $checkInDate = $checkIn ? strtotime($checkIn) : false;
        $checkOutDate = $checkOut ? strtotime($checkOut) : false;

        if (!$checkInDate || $checkInDate < strtotime($today)) {
            $checkIn = $tomorrow;
        }

        if (!$checkOutDate || $checkOutDate <= strtotime($checkIn)) {
            $checkOut = date('Y-m-d', strtotime($checkIn . ' +1 day')); // Ngày sau check-in
        }

        $availableRooms = $this->hotel->getAvailableRooms($id, $checkIn, $checkOut, $guests, $roomType, 3);
        $currentPromotions = $this->hotel->getActivePromotions($id);

        $highlightedAmenities = array_slice($hotel['amenities'] ?? [], 0, 5);

        $images = $this->getHotelImages($id);

        $averageRating = $this->hotel->getAverageRating($id);

        $reviewModel = new \app\Models\Review();
        $ratingDistribution = $reviewModel->getRatingDistribution($id);

        $reviewCount = $this->hotel->getReviewCount($id);

        $contactDetails = $this->getHotelContactDetails($id);
        if (empty($hotel['contact_phone'])) {
            $hotel['contact_phone'] = $contactDetails['phone'] ?? '';
        }
        if (empty($hotel['contact_email'])) {
            $hotel['contact_email'] = $contactDetails['email'] ?? '';
        }

        return $this->view('hotel/show', [
            'hotel' => $hotel,
            'rooms' => $availableRooms,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
            'promotions' => $currentPromotions,
            'highlightedAmenities' => $highlightedAmenities,
            'images' => $images,
            'rating' => $averageRating,
            'reviewCount' => $reviewCount,
            'ratingDistribution' => $ratingDistribution,
            'pageTitle' => $hotel['name']
        ]);
    }

    public function getAvailableRooms()
    {
        try {
            $hotelId = $_POST['hotel_id'] ?? null;
            $checkIn = $_POST['check_in'] ?? null;
            $checkOut = $_POST['check_out'] ?? null;
            $guests = $_POST['guests'] ?? null;
            $roomType = $_POST['room_type'] ?? null;
            if (!$hotelId || !$checkIn || !$checkOut || !$guests) {
                return $this->jsonResponse(['success' => false, 'message' => 'Thiếu thông tin']);
            }

            $availableRooms = $this->hotel->getAvailableRooms($hotelId, $checkIn, $checkOut, $guests, $roomType);
            return $this->jsonResponse(['success' => true, 'rooms' => $availableRooms, 'message' => 'Lấy danh sách phòng thành công']);
        } catch (\Exception $e) {

            return $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }

    // Tạo slug cho tên khách sạn
    private function createSlug($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = $this->removeAccents($str);
        $str = preg_replace('/[^a-z0-9]+/', '-', $str);
        $str = trim($str, '-');
        return $str;
    }

    private function removeAccents($str)
    {
        // Gom nhóm các ký tự theo dạng để dễ quản lý hơn
        $search = [
            'à',
            'á',
            'ạ',
            'ả',
            'ã',
            'â',
            'ầ',
            'ấ',
            'ậ',
            'ẩ',
            'ẫ',
            'ă',
            'ằ',
            'ắ',
            'ặ',
            'ẳ',
            'ẵ',
            'è',
            'é',
            'ẹ',
            'ẻ',
            'ẽ',
            'ê',
            'ề',
            'ế',
            'ệ',
            'ể',
            'ễ',
            'ì',
            'í',
            'ị',
            'ỉ',
            'ĩ',
            'ò',
            'ó',
            'ọ',
            'ỏ',
            'õ',
            'ô',
            'ồ',
            'ố',
            'ộ',
            'ổ',
            'ỗ',
            'ơ',
            'ờ',
            'ớ',
            'ợ',
            'ở',
            'ỡ',
            'ù',
            'ú',
            'ụ',
            'ủ',
            'ũ',
            'ư',
            'ừ',
            'ứ',
            'ự',
            'ử',
            'ữ',
            'ỳ',
            'ý',
            'ỵ',
            'ỷ',
            'ỹ',
            'đ'
        ];

        $replace = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'y',
            'y',
            'y',
            'd'
        ];

        return str_replace($search, $replace, $str);
    }

    // Lấy ảnh khách sạn từ thư mục
    private function getHotelImages($hotelId)
    {
        $hotelImagesPath = ROOT_PATH . '/public/images/hotels/' . $hotelId . '/';
        $hotelImagesUrl = SITE_URL . '/public/images/hotels/' . $hotelId . '/';
        $images = [];

        $defaultImage = SITE_URL . '/public/images/hotels/default.jpg';

        $mainImagePath = $hotelImagesPath . 'main.jpg';
        $images['main'] = file_exists($mainImagePath) ? $hotelImagesUrl . 'main.jpg' : $defaultImage;

        $additionalImages = [];
        if (is_dir($hotelImagesPath)) {
            $files = glob($hotelImagesPath . '*.{jpg,jpeg,png}', GLOB_BRACE);
            if (!empty($files)) {
                foreach ($files as $file) {
                    $fileName = basename($file);
                    if ($fileName !== 'main.jpg') {
                        $additionalImages[] = $hotelImagesUrl . $fileName;
                    }
                }
            }
        }
        if (count($additionalImages) < 6) {
            $additionalImages = $additionalImages + array_fill(count($additionalImages), 6, $defaultImage);
        }

        $images['additional'] = array_slice($additionalImages, 0, 6);

        return $images;
    }

    // Lấy thông tin liên hệ của khách sạn
    private function getHotelContactDetails($hotelId)
    {
        return $this->hotel->getStaffContacts($hotelId);
    }
}
