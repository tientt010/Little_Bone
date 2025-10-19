<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Review;
use app\Models\Hotel;

class ReviewController extends BaseController
{
    private $review;
    private $hotel;
    public function __construct()
    {
        parent::__construct();
        $this->review = new Review();
        $this->hotel = new Hotel();
    }

    // kiểm tra xem người dùng đã đăng nhập hay chưa
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // lấy danh sách đánh giá của khách sạn
    public function getHotelReviews($hotelId)
    {
        $page = (int)$this->getQuery('page', 1);
        $pageSize = (int)$this->getQuery('pageSize', 5);
        $offset = ($page - 1) * $pageSize;

        // Lấy thông tin sắp xếp
        $sortBy = $this->getQuery('sort', 'created_at');
        $sortDirection = $this->getQuery('direction', 'desc');

        if (!in_array($sortBy, ['created_at', 'rating'])) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $stars = $this->getQuery('stars', '');
        $starFilter = [];
        if (!empty($stars)) {
            $starFilter = array_map('intval', explode(',', $stars));
            // Đảm bảo chỉ chấp nhận giá trị từ 1-5
            $starFilter = array_filter($starFilter, function ($value) {
                return $value >= 1 && $value <= 5;
            });
        }

        try {
            $hotel = $this->hotel->find($hotelId);
            if (!$hotel) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Không tìm thấy khách sạn'], 404);
            }

            $reviews = $this->review->getHotelReviews(
                $hotelId,
                $pageSize,
                $offset,
                $sortBy,
                $sortDirection,
                $starFilter
            );



            foreach ($reviews as &$review) {
                try {
                    $images = $this->review->getReviewImages($review['id']);
                    $review['images'] = $images;



                    if (empty($review['comment'])) {
                        $review['comment'] = '';
                    }
                } catch (\Exception $e) {

                    $review['images'] = [];
                }
            }

            return $this->sendJsonResponse([
                'success' => true,
                'reviews' => $reviews
            ]);
        } catch (\Exception $e) {


            return $this->sendJsonResponse([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu đánh giá: ' . $e->getMessage()
            ], 500);
        }
    }

    //Thêm đánh giá mới
    public function addReview()
    {
        if (!isset($_SESSION['user_id'])) {
            return $this->sendJsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá'], 401);
        }

        try {
            $userId = $_SESSION['user_id'];




            $inputData = file_get_contents('php://input');
            $jsonData = json_decode($inputData, true);

            $hotelId = $this->getPost('hotel_id') ?: ($jsonData['hotel_id'] ?? null);
            $rating = $this->getPost('rating') ?: ($jsonData['rating'] ?? null);
            $comment = $this->getPost('comment') ?: ($jsonData['comment'] ?? null);

            if (empty($hotelId)) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Thiếu thông tin khách sạn'], 400);
            }
            if (empty($rating)) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Thiếu thông tin ratting'], 400);
            }

            $rating = (int)$rating;
            if ($rating < 1 || $rating > 5) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Đánh giá phải từ 1-5 sao'], 400);
            }

            $hotel = $this->hotel->find($hotelId);
            if (!$hotel) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Khách sạn không tồn tại'], 404);
            }

            $reviewData = [
                'user_id' => $userId,
                'hotel_id' => $hotelId,
                'rating' => $rating,
                'comment' => $comment
            ];

            $reviewId = $this->review->create($reviewData);

            if (!$reviewId) {
                return $this->sendJsonResponse([
                    'success' => false,
                    'message' => 'Không thể tạo đánh giá: ' . implode(', ', $this->review->getErrors())
                ], 500);
            }

            $uploadedImages = [];
            if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
                $uploadedImages = $this->uploadReviewImages($reviewId, $_FILES['review_images']);
            }

            return $this->sendJsonResponse([
                'success' => true,
                'message' => 'Đánh giá đã được gửi thành công',
                'review_id' => $reviewId,
                'uploaded_images' => $uploadedImages
            ]);
        } catch (\Exception $e) {

            return $this->sendJsonResponse(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    // Xóa đánh giá
    public function deleteReview($reviewId)
    {
        if (!isset($_SESSION['user_id'])) {
            return $this->sendJsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $userId = $_SESSION['user_id'];

        try {
            $review = $this->review->find($reviewId);
            if (!$review) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Không tìm thấy đánh giá'], 404);
            }

            if ($review['user_id'] != $userId && $_SESSION['user_role'] != 'admin') {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Bạn không có quyền xóa đánh giá này'], 403);
            }

            $this->deleteReviewImages($reviewId);

            $result = $this->review->delete($reviewId);
            if (!$result) {
                return $this->sendJsonResponse(['success' => false, 'message' => 'Không thể xóa đánh giá'], 500);
            }

            return $this->sendJsonResponse(['success' => true, 'message' => 'Đã xóa đánh giá thành công']);
        } catch (\Exception $e) {

            return $this->sendJsonResponse(['success' => false, 'message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    // Xử lý upload hình ảnh đánh giá
    private function uploadReviewImages($reviewId, $files)
    {
        $uploadedImages = [];
        $maxFiles = 5;
        $maxSize = 2 * 1024 * 1024;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        $uploadDir = ROOT_PATH . '/public/images/reviews/' . $reviewId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Kiểm tra cấu trúc $_FILES


        $fileCount = count($files['name']);
        if ($fileCount > $maxFiles) {
            $fileCount = $maxFiles;
        }



        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {

                continue;
            }

            $fileType = $files['type'][$i];
            if (!in_array($fileType, $allowedTypes)) {

                continue;
            }

            if ($files['size'][$i] > $maxSize) {

                continue;
            }

            $fileExtension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $newFileName = 'image_' . time() . '_' . $i . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFileName;



            if (move_uploaded_file($files['tmp_name'][$i], $uploadPath)) {
                $uploadedImages[] = $newFileName;
                $result = $this->review->addReviewImage($reviewId, $newFileName);
                if (!$result) {
                } else {
                }
            } else {
            }
        }

        return $uploadedImages;
    }

    // Xóa hình ảnh đánh giá
    private function deleteReviewImages($reviewId)
    {
        $imagesDir = ROOT_PATH . '/public/images/reviews/' . $reviewId . '/';

        if (is_dir($imagesDir)) {
            $files = glob($imagesDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($imagesDir);
        }
    }
}
