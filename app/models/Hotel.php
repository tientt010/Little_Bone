<?php

namespace app\Models;

use core\BaseModel;

class Hotel extends BaseModel
{
    protected $table = 'hotels';

    protected $fillable = [
        'destination_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'description',
        'status'
    ];

    protected $rules = [
        'name' => 'required|max:100',
        'address' => 'required',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'description' => 'required',
        'status' => 'in:active,inactive,pending'
    ];

    // Tìm khách sạn theo ID
    public function search($filters = [], $sort = ['field' => 'avg_rating', 'direction' => 'DESC'])
    {
        try {
            $sql = "SELECT DISTINCT h.*, 
                    MIN(r.price) as min_price,
                    COUNT(DISTINCT r.id) as total_rooms,
                    COUNT(DISTINCT ha.amenity_id) as total_amenities,
                    td.name as city_name,
                    COALESCE(AVG(rev.rating), 0) as avg_rating,
                    COUNT(DISTINCT rev.id) as review_count
                   FROM hotels h
                   LEFT JOIN rooms r ON h.id = r.hotel_id
                   LEFT JOIN hotel_amenities ha ON h.id = ha.hotel_id
                   LEFT JOIN travel_destinations td ON h.destination_id = td.id
                   LEFT JOIN reviews rev ON h.id = rev.hotel_id
                   WHERE h.status = 'active'";
            $params = [];

            if (!empty($filters['city'])) {
                $sql .= " AND td.name LIKE ?";
                $params[] = '%' . $filters['city'] . '%';
            }

            $hasAvgRatingFilter = !empty($filters['avg_rating']);

            if (!empty($filters['price_min'])) {
                $sql .= " AND r.price >= ?";
                $params[] = $filters['price_min'];
            }

            if (!empty($filters['price_max'])) {
                $sql .= " AND r.price <= ?";
                $params[] = $filters['price_max'];
            }

            if (!empty($filters['check_in']) && !empty($filters['check_out'])) {
                $sql .= " AND (
                        SELECT COUNT(*) 
                        FROM rooms r2 
                        WHERE r2.hotel_id = h.id
                        AND r2.status = 'available'
                        AND r2.id NOT IN (
                            SELECT bi.room_id FROM booking_items bi
                            JOIN bookings b ON bi.booking_id = b.id 
                            WHERE bi.booking_status NOT IN ('cancelled', 'completed')
                            AND ((bi.check_in_date <= ? AND bi.check_out_date >= ?)
                            OR (bi.check_in_date <= ? AND bi.check_out_date >= ?))
                        )
                    ) > 0";
                $params[] = $filters['check_out'];
                $params[] = $filters['check_in'];
                $params[] = $filters['check_in'];
                $params[] = $filters['check_out'];
            }

            $sql .= " GROUP BY h.id";

            // Áp dụng điều kiện đánh giá trung bình
            if ($hasAvgRatingFilter) {
                $sql .= " HAVING COALESCE(AVG(rev.rating), 0) >= ?";
                $params[] = $filters['avg_rating'];
            }

            $validSortFields = ['min_price', 'total_rooms', 'name', 'avg_rating'];
            $sortField = isset($sort['field']) && in_array($sort['field'], $validSortFields)
                ? $sort['field']
                : 'avg_rating'; // Sử dụng 'avg_rating' làm giá trị mặc định thay vì 'star_rating'

            $sortDirection = isset($sort['direction']) && strtoupper($sort['direction']) === 'ASC'
                ? 'ASC'
                : 'DESC';

            $sql .= " ORDER BY {$sortField} {$sortDirection}";

            // Thêm giới hạn kết quả nếu có
            if (!empty($filters['limit'])) {
                $sql .= " LIMIT " . (int)$filters['limit'];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            // Thêm thông tin tiện ích cho mỗi khách sạn
            foreach ($results as &$hotel) {
                $hotel['amenities'] = $this->getHotelAmenities($hotel['id']);
                $hotel['available_rooms'] = $this->getAvailableRoomTypes($hotel['id']);

                $hotel['promotions'] = $this->getActivePromotions($hotel['id']);
                if (empty($hotel['image'])) {
                    $hotel['image'] = SITE_URL . '/public/images/hotels/default.jpg';
                } else if (strpos($hotel['image'], 'http') !== 0 && strpos($hotel['image'], SITE_URL) !== 0) {
                    $hotel['image'] = SITE_URL . '/public/images/hotels/' . $hotel['image'];
                }
            }
            return $results;
        } catch (\PDOException $e) {

            $this->errors[] = "Lỗi tìm kiếm: " . $e->getMessage();
            return [];
        }
    }

    // Lấy danh sách tiện ích của khách sạn
    public function getHotelAmenities($hotelId)
    {
        $sql = "SELECT a.* 
                FROM amenities a
                JOIN hotel_amenities ha ON a.id = ha.amenity_id
                WHERE ha.hotel_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId]);
        return $stmt->fetchAll();
    }

    // Câp nhật tiện ích của khách sạn
    public function updateHotelAmenities($hotelId, $amenityIds)
    {
        try {
            $this->db->beginTransaction();
            $deleteSql = "DELETE FROM hotel_amenities WHERE hotel_id = ?";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->execute([$hotelId]);

            if (!empty($amenityIds)) {
                $insertSql = "INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)";
                $insertStmt = $this->db->prepare($insertSql);

                foreach ($amenityIds as $amenityId) {
                    $insertStmt->execute([$hotelId, $amenityId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $this->errors[] = "Lỗi khi cập nhật tiện ích: " . $e->getMessage();

            return false;
        }
    }

    // Lấy danh sách đặt phòng gần đây của khách sạn
    public function getRecentBookings($hotelId)
    {
        try {
            $sql = "SELECT bi.id as booking_item_id, bi.total_price,
                    bi.created_at as booking_date,bi.booking_status as status, 
                    bi.check_in_date as check_in, bi.check_out_date as check_out, 
                    r.room_number
                    FROM booking_items bi
                    LEFT JOIN rooms r ON bi.room_id = r.id
                    WHERE r.hotel_id = ?
                    AND bi.booking_status NOT IN ('cancelled')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Đếm số lượng đặt phòng trong khoảng thời gian
    public function countBookingItems($hotelId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT COUNT(*) as total_bookings
                    FROM booking_items bi
                    JOIN rooms r ON bi.room_id = r.id
                    WHERE r.hotel_id = ?
                    AND bi.booking_status NOT IN ('cancelled')
                    AND bi.created_at >= ? AND bi.created_at <= ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId, $startDate, $endDate]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {

            return 0;
        }
    }

    // Tính tổng doanh thu của khách sạn
    public function getRevenue($hotelId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT SUM(bi.total_price) as total_revenue
                    FROM booking_items bi
                    JOIN rooms r ON bi.room_id = r.id
                    WHERE r.hotel_id = ?
                    AND bi.booking_status NOT IN ('cancelled')
                    AND bi.created_at >= ? AND bi.created_at <= ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId, $startDate, $endDate]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {

            return 0;
        }
    }

    // Tính tỉ lệ lập đầy của khách sạn
    public function getFillRate($hotelId, $startDate, $endDate)
    {
        try {
            $totalRoomsQuery = "SELECT COUNT(*) FROM rooms WHERE hotel_id = ? AND status = 'available'";
            $stmt = $this->db->prepare($totalRoomsQuery);
            $stmt->execute([$hotelId]);
            $totalRooms = (int)$stmt->fetchColumn();

            if ($totalRooms === 0) {
                return 0;
            }

            $startDateTime = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);
            $interval = $startDateTime->diff($endDateTime);
            $totalDays = $interval->days + 1;

            $totalAvailableRoomDays = $totalRooms * $totalDays;

            $sql = "SELECT SUM(
                        DATEDIFF(
                            LEAST(bi.check_out_date, ?),
                            GREATEST(bi.check_in_date, ?)
                        ) + 1
                    ) as total_booked_days
                    FROM booking_items bi
                    JOIN rooms r ON bi.room_id = r.id
                    WHERE r.hotel_id = ?
                    AND bi.booking_status NOT IN ('cancelled')
                    AND bi.check_in_date <= ?
                    AND bi.check_out_date >= ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$endDate, $startDate, $hotelId, $endDate, $startDate]);
            $bookedDays = (float)$stmt->fetchColumn() ?: 0;

            $fillRate = ($bookedDays / $totalAvailableRoomDays) * 100;
            return round($fillRate, 1);
        } catch (\PDOException $e) {

            return 0;
        }
    }

    public function getAvgReview($hotelId, $startDate, $endDate)
    {
        try {
            $sql = "SELECT AVG(rating) as avg_rating
                    FROM reviews
                    WHERE hotel_id = ?
                    AND created_at >= ? AND created_at <= ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId, $startDate, $endDate]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {

            return 0;
        }
    }

    // Lấy thông tin các loại phòng có sẵn của khách sạn
    private function getAvailableRoomTypes($hotelId)
    {
        $sql = "SELECT r.room_type, COUNT(r.id) as room_count, MIN(r.price) as min_price
                FROM rooms r
                WHERE r.hotel_id = ? AND r.status = 'available'
                GROUP BY r.room_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId]);
        return $stmt->fetchAll();
    }

    // Lấy thông tin chi tiết của khách sạn
    public function getDetails($id)
    {
        $hotel = $this->find($id);
        if (!$hotel) return false;
        $sql = "SELECT r.*, r.name as room_name, r.room_type, r.description as room_description
                FROM rooms r
                WHERE r.hotel_id = ? AND r.status = 'available'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $hotel['rooms'] = $stmt->fetchAll();

        // Lấy tiện ích
        $sql = "SELECT a.*
                FROM amenities a
                JOIN hotel_amenities ha ON a.id = ha.amenity_id
                WHERE ha.hotel_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $hotel['amenities'] = $stmt->fetchAll();

        return $hotel;
    }

    // Lấy danh sách phòng có sẵn của khách sạn theo các tiêu chí
    public function getAvailableRooms($hotelId, $checkIn, $checkOut, $guests, $roomType, $limit = null)
    {
        // Sửa SQL để lấy thông tin room_type trực tiếp từ bảng rooms
        // và kiểm tra xung đột booking trong bảng booking_items thay vì bookings
        $sql = "SELECT r.*, r.name as room_name, r.room_type 
                FROM rooms r
                WHERE r.hotel_id = ?
                AND r.status = 'available'
                AND r.capacity >= ?"
            . ($roomType ? " AND r.room_type = ?" : "")
            . " AND r.id NOT IN (
                    SELECT bi.room_id
                    FROM booking_items bi
                    WHERE bi.booking_status NOT IN ('cancelled', 'completed')
                    AND ((bi.check_in_date <= ? AND bi.check_out_date >= ?)
                    OR (bi.check_in_date <= ? AND bi.check_out_date >= ?))
                )";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->db->prepare($sql);
        if ($roomType) {
            $stmt->execute([$hotelId, $guests, $roomType, $checkOut, $checkIn, $checkIn, $checkOut]);
        } else {
            $stmt->execute([$hotelId, $guests, $checkOut, $checkIn, $checkIn, $checkOut]);
        }
        return $stmt->fetchAll();
    }

    // Thêm tiện ích cho khách sạn
    public function addAmenities($hotelId, $amenityIds)
    {
        try {
            $sql = "INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($amenityIds as $amenityId) {
                $stmt->execute([$hotelId, $amenityId]);
            }
            return true;
        } catch (\PDOException $e) {
            $this->errors[] = "Lỗi khi thêm tiện ích: " . $e->getMessage();
            return false;
        }
    }


    // Lấy danh sách khách sạn nổi bật
    public function getFeaturedHotels($limit = 6)
    {
        try {
            $sql = "SELECT h.*, 
                    MIN(r.price) as min_price,
                    COUNT(DISTINCT r.id) as total_rooms,
                    COALESCE(AVG(rb.rating), 0) as avg_rating,
                    COUNT(DISTINCT rb.id) as review_count
                   FROM hotels h
                   LEFT JOIN rooms r ON h.id = r.hotel_id
                   LEFT JOIN room_bookings rb ON h.id = rb.hotel_id
                   WHERE h.status = 'active'
                   GROUP BY h.id
                   HAVING total_rooms > 0
                   ORDER BY avg_rating DESC, review_count DESC
                   LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            $hotels = $stmt->fetchAll();

            foreach ($hotels as &$hotel) {
                $hotel['image'] = $this->getHotelImage($hotel['id']);
            }

            return $hotels;
        } catch (\PDOException $e) {
            $this->errors[] = "Lỗi khi lấy danh sách khách sạn nổi bật: " . $e->getMessage();
            return [];
        }
    }

    // Lấy danh sách thành phố phổ biến
    public function getPopularCities($limit = 6)
    {
        try {
            $limit = (int) $limit;
            $sql = "SELECT h.city,
                    COUNT(DISTINCT h.id) as hotel_count,
                    COUNT(DISTINCT b.id) as booking_count
                   FROM hotels h
                   LEFT JOIN rooms r ON h.id = r.hotel_id
                   LEFT JOIN bookings b ON r.id = b.room_id
                   GROUP BY h.city 
                   ORDER BY booking_count DESC
                   LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $cities = $stmt->fetchAll();

            foreach ($cities as &$city) {
                $city['name'] = $city['city'];
                $city['image'] = $this->getCityImage($city['city']);
            }

            return $cities;
        } catch (\PDOException $e) {
            $this->errors[] = "Lỗi khi lấy danh sách thành phố phổ biến: " . $e->getMessage();

            return [];
        }
    }

    // Lấy hình ảnh đại diện của khách sạn
    private function getHotelImage($hotelId)
    {
        try {
            $sql = "SELECT image_path FROM images WHERE entity_type = 'hotel' AND entity_id = ? AND is_primary = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId]);
            $result = $stmt->fetch();

            if ($result && !empty($result['image_path'])) {
                return SITE_URL . '/public/images/hotels/' . $result['image_path'];
            }

            $sql = "SELECT image FROM hotels WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId]);
            $result = $stmt->fetch();

            if ($result && !empty($result['image'])) {
                return SITE_URL . '/public/images/hotels/' . $result['image'];
            }
        } catch (\Exception $e) {
        }

        // Trả về hình ảnh mặc định nếu không tìm thấy
        return SITE_URL . '/public/images/hotels/default.jpg';
    }

    // 
    private function getCityImage($cityName)
    {
        try {
            $sql = "SELECT image FROM travel_destinations WHERE name = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cityName]);
            $result = $stmt->fetch();

            if ($result && !empty($result['image'])) {
                return SITE_URL . '/public/images/cities/' . $result['image'];
            }
        } catch (\Exception $e) {
        }

        // Trả về hình ảnh mặc định
        return SITE_URL . '/public/images/cities/default.jpg';
    }

    // Kiểm tra xem khách sạn có trong danh sách yêu thích của người dùng hay không
    public function isFavorite($hotelId, $userId)
    {
        $sql = "SELECT COUNT(*) as count FROM favorites 
                WHERE hotel_id = ? AND user_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId, $userId]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    // Thêm khách sạn vào danh sách yêu thích
    public function addToFavorites($hotelId, $userId)
    {
        if ($this->isFavorite($hotelId, $userId)) {
            return true;
        }

        $sql = "INSERT INTO favorites (user_id, hotel_id, created_at) 
                VALUES (?, ?, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $hotelId]);
    }

    // Xóa khách sạn khỏi danh sách yêu thích
    public function removeFromFavorites($hotelId, $userId)
    {
        $sql = "DELETE FROM favorites 
                WHERE hotel_id = ? AND user_id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hotelId, $userId]);
    }

    // Lấy danh sách khách sạn yêu thích của người dùng
    public function getFavoritesByUser($userId, $limit = 10, $offset = 0)
    {
        $userId = (int)$userId;
        $limit = (int)$limit;
        $offset = (int)$offset;

        $sql = "SELECT h.*, f.created_at as favorited_at
                FROM hotels h
                INNER JOIN favorites f ON h.id = f.hotel_id 
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy đánh giá gần đây của khách sạn
    public function getRecentReviews($hotelId, $limit = 5, $offset = 0)
    {
        $sql = "SELECT r.id, u.avatar as user_avatar, u.full_name as user_name, r.rating, r.created_at as review_date, r.comment
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.hotel_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        // Bind parameters with explicit types
        $stmt->bindParam(1, $hotelId, \PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, \PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Lấy điểm đánh giá trung bình của khách sạn trong khoảng thời gian
    public function getAverageRating($hotelId, $startDay = null, $endDay = null)
    {
        // Nếu không có ngày bắt đầu và kết thúc, lấy tất cả
        if (is_null($startDay) || is_null($endDay)) {
            $startDay = '1970-01-01';
            $endDay = date('Y-m-d H:i:s');
        }

        $sql = "SELECT COALESCE(AVG(rating), 0) as avg_rating 
                FROM reviews 
                WHERE hotel_id = ? AND created_at BETWEEN ? AND ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId, $startDay, $endDay]);
        $result = $stmt->fetch();

        return round($result['avg_rating'], 1);
    }

    // Lấy số lượng đánh giá của khách sạn
    public function getReviewCount($hotelId)
    {
        $sql = "SELECT COUNT(*) as count FROM reviews WHERE hotel_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId]);
        $result = $stmt->fetch();
        return (int)$result['count'];
    }

    // Lấy danh sách khách sạn theo điểm đến
    public function getHotelsByDestination($destinationId, $limit = 4)
    {
        try {
            $sql = "SELECT h.*, MIN(r.price) as min_price, COALESCE(AVG(rev.rating), 0) as avg_rating
                    FROM hotels h
                    LEFT JOIN rooms r ON h.id = r.hotel_id
                    LEFT JOIN reviews rev ON h.id = rev.hotel_id
                    WHERE h.destination_id = ? AND h.status = 'active'
                    GROUP BY h.id
                    ORDER BY min_price DESC
                    LIMIT " . (int) $limit;


            $stmt = $this->db->prepare($sql);
            $stmt->execute([$destinationId]);
            $hotels = $stmt->fetchAll();

            foreach ($hotels as &$hotel) {
                if (empty($hotel['image'])) {
                    $hotel['image'] = SITE_URL . '/public/images/hotels/default.jpg';
                } else if (strpos($hotel['image'], 'http') !== 0 && strpos($hotel['image'], SITE_URL) !== 0) {
                    $hotel['image'] = SITE_URL . '/public/images/hotels/' . $hotel['image'];
                }
            }

            return $hotels;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Lấy thông tin liên hệ của nhân viên khách sạn
    public function getStaffContacts($hotelId)
    {
        try {
            $sql = "SELECT u.phone, u.email 
                    FROM users u 
                    JOIN hotel_staff hs ON u.id = hs.user_id 
                    WHERE hs.hotel_id = ? 
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId]);
            return $stmt->fetch() ?: ['phone' => '', 'email' => ''];
        } catch (\PDOException $e) {

            return ['phone' => '', 'email' => ''];
        }
    }

    // Lấy thông tin chi tiết của khách sạn theo ID
    public function getHotelById($hotelId)
    {
        $hotelId = (int) $hotelId;
        $sql = "SELECT h.* 
                FROM hotels h
                WHERE h.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            $ratingsSql = "SELECT AVG(rating) as average_rating, COUNT(*) as review_count 
                          FROM reviews 
                          WHERE hotel_id = ?";

            $ratingsStmt = $this->db->prepare($ratingsSql);
            $ratingsStmt->execute([$hotelId]);
            $ratings = $ratingsStmt->fetch(\PDO::FETCH_ASSOC);

            if ($ratings) {
                $result['average_rating'] = $ratings['average_rating'];
                $result['review_count'] = $ratings['review_count'];
            }

            return $result;
        }

        return false;
    }

    // Lấy danh sách khuyến mãi đang hoạt động của khách sạn
    public function getActivePromotions($hotelId)
    {
        $today = date('Y-m-d');
        $sql = "SELECT p.* 
                FROM promotions p
                JOIN hotel_promotions hp ON p.id = hp.promotion_id
                WHERE hp.hotel_id = ? 
                AND p.status = 'active'
                AND p.start_date <= ? 
                AND p.end_date >= ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hotelId, $today, $today]);
        return $stmt->fetchAll();
    }

    // Kiểm tra quyền truy cập của nhân viên khách sạn
    public function checkStaffAccess($userId, $hotelId)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM hotel_staff 
                    WHERE user_id = ? AND hotel_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $hotelId]);
            $result = $stmt->fetch();
            return ($result['count'] > 0);
        } catch (\PDOException $e) {

            return false;
        }
    }

    public function getRoomsbyHotelId($hotelId)
    {
        try {
            $sql = "SELECT * FROM rooms WHERE hotel_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Lấy số lượng đặt phòng theo loại phòng trong khoảng thời gian
    public function roomTypeBookings($hotelId, $startDate, $endDate, $roomType)
    {
        try {
            $sql = "SELECT COUNT(*) as booking_count
                    FROM booking_items bi
                    JOIN rooms r ON bi.room_id = r.id
                    WHERE r.hotel_id = ?
                    AND r.room_type = ?
                    AND bi.booking_status NOT IN ('cancelled')
                    AND bi.created_at BETWEEN ? AND ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$hotelId, $roomType, $startDate, $endDate]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {

            return 0;
        }
    }
}
