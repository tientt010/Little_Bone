<?php

namespace app\Models;

use core\BaseModel;

class Review extends BaseModel
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'booking_id',
        'rating',
        'comment'
    ];

    protected $rules = [
        'user_id' => 'required|numeric',
        'hotel_id' => 'required|numeric',
        'booking_id' => 'numeric', // Changed from 'required|numeric' to just 'numeric'
        'rating' => 'required|numeric|min:1|max:5'
    ];

    // Lấy danh sách đánh giá của một khách sạn
    public function getHotelReviews($hotelId, $limit = 10, $offset = 0, $sortBy = 'created_at', $sortDirection = 'desc', $starFilter = [])
    {
        try {
            // Đảm bảo các tham số là số nguyên
            $hotelId = (int)$hotelId;
            $limit = (int)$limit;
            $offset = (int)$offset;

            // Validate sortBy
            if (!in_array($sortBy, ['created_at', 'rating'])) {
                $sortBy = 'created_at';
            }

            // Validate sortDirection
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $sql = "SELECT r.*, u.full_name, u.avatar
                    FROM {$this->table} r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.hotel_id = ?";

            $params = [$hotelId];

            // Thêm điều kiện lọc theo số sao
            if (!empty($starFilter)) {
                $placeholders = implode(',', array_fill(0, count($starFilter), '?'));
                $sql .= " AND r.rating IN ($placeholders)";
                $params = array_merge($params, $starFilter);
            }

            // Thêm điều kiện sắp xếp
            $sql .= " ORDER BY r.{$sortBy} {$sortDirection}";

            // Thêm giới hạn và offset
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);

            // Bind params
            foreach ($params as $i => $param) {
                $stmt->bindValue($i + 1, $param, \PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (\Exception $e) {

            return [];
        }
    }

    // Lấy hình ảnh của đánh giá
    public function getReviewImages($reviewId)
    {
        try {
            $checkTableSql = "SHOW TABLES LIKE 'review_images'";
            $checkStmt = $this->db->query($checkTableSql);

            if ($checkStmt->rowCount() === 0) {

                return [];
            }

            $sql = "SELECT image_path 
                    FROM review_images 
                    WHERE review_id = ?
                    ORDER BY id ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reviewId]);

            $result = [];
            while ($row = $stmt->fetch()) {
                $result[] = $row['image_path'];
            }

            return $result;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Them hình ảnh vào đánh giá
    public function addReviewImage($reviewId, $imagePath)
    {
        try {
            $checkReviewSql = "SELECT id FROM reviews WHERE id = ?";
            $checkReviewStmt = $this->db->prepare($checkReviewSql);
            $checkReviewStmt->execute([$reviewId]);

            if ($checkReviewStmt->rowCount() === 0) {

                return false;
            }

            $sql = "INSERT INTO review_images (review_id, image_path) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$reviewId, $imagePath]);



            return $result;
        } catch (\PDOException $e) {

            return false;
        }
    }

    // Xoá ảnh của đánh giá
    public function deleteReviewImages($reviewId)
    {
        $sql = "DELETE FROM review_images WHERE review_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$reviewId]);
    }

    // Lấy phân phối đánh giá của một khách sạn
    public function getRatingDistribution($hotelId)
    {
        $sql = "SELECT rating, COUNT(*) as count
                FROM {$this->table}
                WHERE hotel_id = ?
                GROUP BY rating
                ORDER BY rating DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId]);
        $results = $stmt->fetchAll();

        $distribution = [];
        $total = 0;

        foreach ($results as $row) {
            $distribution[$row['rating']] = $row['count'];
            $total += $row['count'];
        }

        // Chuyển đổi thành phần trăm
        if ($total > 0) {
            foreach ($distribution as $rating => $count) {
                $distribution[$rating] = round(($count / $total) * 100);
            }
        }

        // Đảm bảo có đủ 5 mức đánh giá
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($distribution[$i])) {
                $distribution[$i] = 0;
            }
        }

        return $distribution;
    }

    // Tao một đánh giá mới
    public function create($data)
    {
        try {
            // Kiểm tra dữ liệu có hợp lệ không
            if (empty($data['user_id']) || empty($data['hotel_id']) || empty($data['rating'])) {
                $this->errors[] = 'Thiếu thông tin bắt buộc';
                return false;
            }

            $sql = "INSERT INTO reviews (user_id, hotel_id, rating, comment, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['hotel_id'],
                $data['rating'],
                $data['comment'] ?? ''  // Đảm bảo comment được xử lý đúng cách
            ]);

            $reviewId = $this->db->lastInsertId();



            return $reviewId;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }
    }

    // Xoá 1 đánh giá
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);



            return $result;
        } catch (\Exception $e) {

            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // Tìm đánh giá theo ID
    public function findById($id, $columns = ['*'])
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $result = $stmt->fetch();

            if ($result) {

                return $result;
            } else {

                return false;
            }
        } catch (\Exception $e) {

            $this->errors[] = $e->getMessage();
            return false;
        }
    }
}
