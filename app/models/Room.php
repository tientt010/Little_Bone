<?php

namespace app\Models;

use core\BaseModel;

class Room extends BaseModel
{
    const STATUS_AVAILABLE = 'available';
    const STATUS_BOOKED = 'booked';
    const STATUS_MAINTENANCE = 'maintenance';
    const BOOKING_PENDING = 'pending';
    const BOOKING_CONFIRMED = 'confirmed';
    const BOOKING_CANCELLED = 'cancelled';
    const BOOKING_COMPLETED = 'completed';

    protected $table = 'rooms';

    // Cập nhật fillable theo đúng schema
    protected $fillable = [
        'hotel_id',
        'room_number',
        'name',
        'room_type',
        'price',
        'description',
        'area',
        'capacity',
        'status'
    ];

    protected $rules = [
        'hotel_id' => 'required|numeric',
        'room_number' => 'required|max:20',
        'name' => 'required|max:100',
        'room_type' => 'required|in:single,double,suite,family',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|max:1000',
        'area' => 'nullable|numeric|min:0',
        'capacity' => 'required|numeric|min:1',
        'status' => 'required|in:available,booked,maintenance'
    ];

    /**
     * Magic getter to access properties
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * Magic setter to set properties
     * @param string $name Property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function load($id)
    {
        $data = $this->find($id);
        if ($data) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return true;
        }
        return false;
    }

    // kiểm tra xung đột booking
    private function validateDates($checkInDate, $checkOutDate)
    {
        $today = date('Y-m-d');
        $checkIn = date('Y-m-d', strtotime($checkInDate));
        $checkOut = date('Y-m-d', strtotime($checkOutDate));

        if ($checkIn < $today) {
            $this->errors[] = "Ngày check-in không thể trong quá khứ";
            return false;
        }

        if ($checkOut <= $checkIn) {
            $this->errors[] = "Ngày check-out phải sau ngày check-in";
            return false;
        }

        return true;
    }

    // Tính toán tổng giá tiền cho khoảng thời gian đặt phòng
    public function calculateTotalPrice($checkInDate, $checkOutDate)
    {
        if (!$this->validateDates($checkInDate, $checkOutDate)) {
            return false;
        }

        if (!$this->price) {
            $this->errors[] = "Chưa có thông tin giá phòng";
            return false;
        }

        $nights = (strtotime($checkOutDate) - strtotime($checkInDate)) / (60 * 60 * 24);

        if ($nights < 1) {
            $this->errors[] = "Thời gian đặt phòng không hợp lệ";
            return false;
        }

        return round($this->price * $nights, 2);
    }

    // Cập nhật trạng thái phòng
    public function updateStatus($newStatus)
    {
        $validStatuses = [
            self::STATUS_AVAILABLE,
            self::STATUS_BOOKED,
            self::STATUS_MAINTENANCE
        ];

        if (!in_array($newStatus, $validStatuses)) {
            $this->errors[] = "Invalid room status: {$newStatus}";
            return false;
        }

        return $this->update($this->id, ['status' => $newStatus]);
    }

    // Lây thông tin khách sạn liên kết với phòng
    public function getHotelDetails()
    {
        if (!$this->hotel_id) {
            $this->errors[] = "Chưa có thông tin khách sạn";
            return false;
        }

        $sql = "SELECT h.*, COUNT(r.id) as total_rooms
                FROM hotels h
                LEFT JOIN rooms r ON h.id = r.hotel_id
                WHERE h.id = ?
                GROUP BY h.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->hotel_id]);
        return $stmt->fetch();
    }

    // Lấy thông tin loại phòng
    public function getRoomTypeDetails()
    {
        if (!$this->room_type) {
            $this->errors[] = "Chưa có thông tin loại phòng";
            return false;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'room_type' => $this->room_type,
            'description' => $this->description,
            'base_capacity' => $this->capacity,
            'max_capacity' => $this->capacity
        ];
    }


    public function getBookingHistory($filters = [])
    {
        $sql = "SELECT b.*, 
                       u.full_name as guest_name,
                       u.email as guest_email,
                       u.phone as guest_phone,
                       DATEDIFF(b.check_out_date, b.check_in_date) as nights,
                       r.name as room_type_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.id
                WHERE b.room_id = ?";

        $params = [$this->id];

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND b.check_in_date >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND b.check_out_date <= ?";
            $params[] = $filters['to_date'];
        }

        $sql .= " ORDER BY b.check_in_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    // Lấy danh sách các booking đã hoàn thành của phòng
    public function getCompletedBookings($roomId, $limit = 5)
    {
        $sql = "SELECT b.*, u.full_name, u.avatar
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE b.room_id = ? 
                AND b.booking_status = ?
                ORDER BY b.check_out_date DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId, self::BOOKING_COMPLETED, $limit]);
        return $stmt->fetchAll();
    }

    // Lấy danh sách tiện nghi của phòng
    public function getRoomAmenities($roomId)
    {
        $sql = "SELECT ral.* 
                FROM room_amenities ra
                JOIN room_amenities_list ral ON ra.amenity_id = ral.id
                WHERE ra.room_id = ?
                ORDER BY ral.is_premium DESC, ral.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId]);
        return $stmt->fetchAll();
    }

    // Lấy thông tin phòng theo ID
    public function getRoomById($roomId)
    {
        $roomId = (int) $roomId;

        $sql = "SELECT r.*, h.name as hotel_name, h.address as hotel_address 
                FROM rooms r
                LEFT JOIN hotels h ON r.hotel_id = h.id
                WHERE r.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: false;
    }

    public function search($hotelId, $filters = [])
    {
        try {
            $sql = "SELECT * 
                    FROM rooms
                    WHERE hotel_id = ?";

            $params = [$hotelId];

            if (!empty($filters['room_type'])) {
                $sql .= " AND room_type = ?";
                $params[] = $filters['room_type'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['min_price'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['max_price'];
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            // Log lỗi nếu cần

            return [];
        }
    }

    // Lấy chi tiết phòng bao gồm tiện nghi
    public function getDetailRoom($roomId)
    {
        try {
            $sql = "SELECT
                        r.*,
                        rl.id as amenity_id,
                        rl.name as amenity_name,
                        rl.description as amenity_description,
                        rl.icon as amenity_icon,
                        rl.is_premium as amenity_is_premium
                    FROM rooms r
                    LEFT JOIN room_amenities ra ON r.id = ra.room_id
                    LEFT JOIN room_amenities_list rl ON ra.amenity_id = rl.id
                    WHERE r.id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$roomId]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($rows && !empty($rows)) {
                // Khởi tạo dữ liệu phòng từ hàng đầu tiên
                $roomData = [];
                $amenities = [];
                $firstRow = $rows[0];

                // Trích xuất các cột thông tin phòng
                foreach ($firstRow as $key => $value) {
                    if (strpos($key, 'amenity_') !== 0) {
                        $roomData[$key] = $value;
                    }
                }

                // Trích xuất tiện nghi từ tất cả các hàng
                foreach ($rows as $row) {
                    if (!empty($row['amenity_id'])) {
                        $amenity = [
                            'id' => $row['amenity_id'],
                            'name' => $row['amenity_name'],
                            'description' => $row['amenity_description'],
                            'icon' => $row['amenity_icon'],
                            'is_premium' => $row['amenity_is_premium']
                        ];
                        $amenities[] = $amenity;
                    }
                }

                // Thêm mảng tiện nghi vào dữ liệu phòng
                $roomData['amenities'] = $amenities;

                return $roomData;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            // Log lỗi nếu cần

            return [];
        }
    }
}
