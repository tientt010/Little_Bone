<?php

namespace app\Models;

use core\BaseModel;

class Booking extends BaseModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const PAYMENT_UNPAID = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_CANCELLED = 'cancelled';

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'payment_status'
    ];

    protected $rules = [
        'user_id' => 'required|numeric',
        'total_price' => 'required|numeric|min:0',
        'payment_method' => 'required|in:credit_card,at_hotel,bank_transfer,paypal',
        'payment_status' => 'required|in:pending,paid,refunded,cancelled'
    ];

    // Lấy tất cả các booking items của một booking
    public function getBookingItems($bookingId = null)
    {
        if ($bookingId === null) {
            $bookingId = $this->id ?? 0;
        }

        $sql = "SELECT * FROM booking_items WHERE booking_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll();
    }

    // tạo booking với các booking items
    public function create($data, $items = [])
    {
        if (!$this->validate($data, $this->rules)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $bookingId = parent::create($data);
            if (!$bookingId) {
                throw new \Exception("Không thể tạo booking");
            }

            if (!empty($items)) {
                foreach ($items as $item) {
                    $sql = "SELECT * FROM rooms WHERE id = ? FOR UPDATE";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$item['room_id']]);
                    $room = $stmt->fetch();

                    if (!$room) {
                        throw new \Exception("Phòng ID {$item['room_id']} không tồn tại");
                    }

                    if ($room['status'] !== Room::STATUS_AVAILABLE) {
                        throw new \Exception("Phòng ID {$item['room_id']} không khả dụng");
                    }

                    if ($this->hasBookingConflict($item['room_id'], $item['check_in_date'], $item['check_out_date'])) {
                        throw new \Exception("Phòng ID {$item['room_id']} đã được đặt trong khoảng thời gian này");
                    }

                    $item['booking_id'] = $bookingId;

                    if (!isset($item['room_name']) && isset($room['name'])) {
                        $item['room_name'] = $room['name'];
                    }

                    if (!isset($item['nights'])) {
                        $checkIn = new \DateTime($item['check_in_date']);
                        $checkOut = new \DateTime($item['check_out_date']);
                        $nights = $checkOut->diff($checkIn)->days;
                        $item['nights'] = $nights > 0 ? $nights : 1;
                    }

                    if (!isset($item['total_price'])) {
                        $item['total_price'] = $item['unit_price'] * $item['nights'];
                    }
                    $this->createBookingItem($item);

                    $sql = "UPDATE rooms SET status = ? WHERE id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([Room::STATUS_BOOKED, $item['room_id']]);
                }
            }

            $this->db->commit();
            return $bookingId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // Tạo một booking item
    private function createBookingItem($data)
    {
        $fields = [
            'booking_id',
            'room_id',
            'room_name',
            'check_in_date',
            'check_out_date',
            'booking_status',
            'unit_price',
            'nights',
            'total_price',
            'notes'
        ];

        $placeholders = [];
        $values = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $placeholders[] = $field;
                $values[] = $data[$field];
            }
        }

        $sql = "INSERT INTO booking_items (" . implode(", ", $placeholders) .
            ") VALUES (" . implode(", ", array_fill(0, count($placeholders), "?")) . ")";

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($values)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    // kiểm tra xung đột booking
    private function hasBookingConflict($roomId, $checkIn, $checkOut)
    {
        $sql = "SELECT COUNT(*) FROM booking_items
                WHERE room_id = ?
                AND booking_status NOT IN (?, ?)
                AND (
                    (check_in_date < ? AND check_out_date > ?)
                    OR (check_in_date < ? AND check_out_date > ?)
                    OR (check_in_date >= ? AND check_out_date <= ?)
                    OR (? BETWEEN check_in_date AND check_out_date)
                    OR (? BETWEEN check_in_date AND check_out_date)
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $roomId,
            self::STATUS_CANCELLED,
            self::STATUS_COMPLETED,
            $checkOut,
            $checkIn,
            $checkIn,
            $checkIn,
            $checkIn,
            $checkOut,
            $checkIn,
            $checkOut
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    // cập nhật trạng thái
    public function updateItemStatus($itemId, $status)
    {
        $sql = "UPDATE booking_items SET booking_status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$status, $itemId]);

        // Nếu trạng thái là cancelled hoặc completed, cập nhật trạng thái phòng
        if ($result && ($status == self::STATUS_CANCELLED || $status == self::STATUS_COMPLETED)) {
            $sql = "SELECT room_id FROM booking_items WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId]);
            $roomId = $stmt->fetchColumn();

            if ($roomId) {
                $room = new Room();
                $room->updateStatus(Room::STATUS_AVAILABLE, $roomId);
            }
        }

        return $result;
    }

    // cập nhật trạng thái thanh toán
    public function updatePaymentStatus($id, $paymentStatus)
    {
        $booking = $this->find($id);
        if (!$booking) {
            $this->errors[] = "Booking không tồn tại";
            return false;
        }

        try {
            $sql = "UPDATE bookings SET payment_status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$paymentStatus, $id]);
        } catch (\PDOException $e) {
            $this->errors[] = "Lỗi cập nhật: " . $e->getMessage();
            return false;
        }
    }

    // Hủy booking
    public function cancel($id)
    {
        $booking = $this->find($id);
        if (!$booking) {
            $this->errors[] = "Booking không tồn tại";
            return false;
        }

        try {
            $this->db->beginTransaction();

            $this->updatePaymentStatus($id, self::PAYMENT_CANCELLED);

            $sql = "UPDATE booking_items SET booking_status = ? WHERE booking_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([self::STATUS_CANCELLED, $id]);

            $sql = "SELECT room_id FROM booking_items WHERE booking_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $roomIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($roomIds)) {
                $room = new Room();
                foreach ($roomIds as $roomId) {
                    $room->updateStatus(Room::STATUS_AVAILABLE, $roomId);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // xác nhân thanh toán
    public function confirmPayment($id)
    {
        $booking = $this->find($id);
        if (!$booking) {
            $this->errors[] = "Booking không tồn tại";
            return false;
        }

        if ($booking['payment_status'] != self::PAYMENT_UNPAID) {
            $this->errors[] = "Trạng thái thanh toán không hợp lệ";
            return false;
        }

        try {
            $this->db->beginTransaction();

            $this->updatePaymentStatus($id, self::PAYMENT_PAID);

            $sql = "UPDATE booking_items SET booking_status = ? WHERE booking_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([self::STATUS_CONFIRMED, $id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // lấy chi tiết booking
    public function getDetails($id)
    {
        $booking = $this->find($id);
        if (!$booking) {
            return false;
        }

        // Lấy thông tin người dùng
        $sql = "SELECT u.full_name as guest_name, u.email as guest_email, u.phone as guest_phone
               FROM users u
               WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$booking['user_id']]);
        $user = $stmt->fetch();

        if ($user) {
            $booking = array_merge($booking, $user);
        }

        // Lấy thông tin items
        $sql = "SELECT bi.*, h.name as hotel_name
               FROM booking_items bi
               JOIN rooms r ON bi.room_id = r.id
               JOIN hotels h ON r.hotel_id = h.id
               WHERE bi.booking_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $booking['items'] = $stmt->fetchAll();

        return $booking;
    }

    // Lấy danh sách booking của người dùng theo trạng thái
    public function getByUserAndStatus($userId, $status = 'all', $limit = 10, $offset = 0)
    {
        $params = [$userId];

        $sql = "SELECT b.*, COUNT(bi.id) as total_items
                FROM bookings b
                LEFT JOIN booking_items bi ON b.id = bi.booking_id
                WHERE b.user_id = ?";

        if ($status !== 'all') {
            $sql .= " AND EXISTS (SELECT 1 FROM booking_items bi2 WHERE bi2.booking_id = b.id AND bi2.booking_status = ?)";
            $params[] = $status;
        }

        $sql .= " GROUP BY b.id ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        if (!empty($bookings)) {
            foreach ($bookings as &$booking) {
                $sql = "SELECT bi.*, h.name as hotel_name, h.image as hotel_image
                       FROM booking_items bi
                       JOIN rooms r ON bi.room_id = r.id
                       JOIN hotels h ON r.hotel_id = h.id
                       WHERE bi.booking_id = ?
                       LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$booking['id']]);
                $firstItem = $stmt->fetch();

                if ($firstItem) {
                    $booking['first_item'] = $firstItem;
                }
            }
        }

        return $bookings;
    }

    // lấy danh sách booking của người dùng theo khoảng thời gian
    public function getByUserAndDateRange($userId, $startDate, $endDate, $limit = 10, $offset = 0)
    {
        $sql = "SELECT b.*, COUNT(bi.id) as total_items
                FROM bookings b
                JOIN booking_items bi ON b.id = bi.booking_id
                WHERE b.user_id = ?
                AND EXISTS (
                    SELECT 1 FROM booking_items bi2
                    WHERE bi2.booking_id = b.id
                    AND ((bi2.check_in_date BETWEEN ? AND ?)
                    OR (bi2.check_out_date BETWEEN ? AND ?))
                )
                GROUP BY b.id
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $userId,
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $limit,
            $offset
        ]);

        $bookings = $stmt->fetchAll();

        if (!empty($bookings)) {
            foreach ($bookings as &$booking) {
                $sql = "SELECT bi.*, h.name as hotel_name, h.image as hotel_image
                       FROM booking_items bi
                       JOIN rooms r ON bi.room_id = r.id
                       JOIN hotels h ON r.hotel_id = h.id
                       WHERE bi.booking_id = ?
                       LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$booking['id']]);
                $firstItem = $stmt->fetch();

                if ($firstItem) {
                    $booking['first_item'] = $firstItem;
                }
            }
        }

        return $bookings;
    }

    // Xác thực quyền hủy booking của người dùng
    public function validateUserCancel($userId, $bookingId)
    {
        $sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $this->errors[] = 'Không tìm thấy đặt phòng hoặc bạn không có quyền hủy';
            return false;
        }

        // Kiểm tra xem tất cả các items có thể hủy không
        $sql = "SELECT COUNT(*) FROM booking_items 
                WHERE booking_id = ? 
                AND booking_status NOT IN (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId, self::STATUS_PENDING, self::STATUS_CONFIRMED]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $this->errors[] = 'Một số phòng trong đơn đặt này không thể hủy ở trạng thái hiện tại';
            return false;
        }

        return $booking;
    }

    // Tạo booking với các booking items
    public function createBookingWithItems($bookingData, $bookingItems)
    {
        try {
            // Bắt đầu transaction
            $this->db->beginTransaction();

            $query = "INSERT INTO bookings (user_id, customer_name, customer_phone, customer_email, total_price, discount, taxes, final_amount, payment_method, payment_status, notes, created_at, updated_at) 
                      VALUES (:user_id, :customer_name, :customer_phone, :customer_email, :total_price, :discount, :taxes, :final_amount, :payment_method, :payment_status, :notes, NOW(), NOW())";

            $params = [
                ':user_id' => $bookingData['user_id'],
                ':total_price' => $bookingData['total_price'],
                ':discount' => $bookingData['discount'],
                ':taxes' => $bookingData['taxes'],
                ':final_amount' => $bookingData['final_amount'],
                ':payment_method' => $bookingData['payment_method'],
                ':payment_status' => $bookingData['payment_status'],
                ':customer_name' => $bookingData['customer_name'],
                ':customer_phone' => $bookingData['customer_phone'],
                ':customer_email' => $bookingData['customer_email'],
                ':notes' => $bookingData['notes']
            ];

            $stmt = $this->db->prepare($query);
            $success = $stmt->execute($params);

            if (!$success) {
                throw new \Exception("Không thể tạo đơn đặt phòng");
            }

            $bookingId = $this->db->lastInsertId();

            foreach ($bookingItems as $item) {
                $query = "INSERT INTO booking_items (booking_id, room_id, check_in_date, check_out_date, 
                         booking_status, unit_price, nights, total_price, created_at, updated_at)
                         VALUES (:booking_id, :room_id, :check_in_date, :check_out_date, 
                         :booking_status, :unit_price, :nights, :total_price, NOW(), NOW())";

                $params = [
                    ':booking_id' => $bookingId,
                    ':room_id' => $item['room_id'],
                    ':check_in_date' => $item['check_in_date'],
                    ':check_out_date' => $item['check_out_date'],
                    ':booking_status' => 'pending',
                    ':unit_price' => $item['unit_price'],
                    ':nights' => $item['nights'],
                    ':total_price' => $item['total_price'],
                ];

                $stmt = $this->db->prepare($query);
                $success = $stmt->execute($params);

                if (!$success) {
                    throw new \Exception("Không thể thêm chi tiết đặt phòng");
                }
            }

            $this->db->commit();

            return $bookingId;
        } catch (\Exception $e) {
            $this->db->rollback();

            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    // Lấy danh sách khách sạn đã đặt của người dùng
    public function getUserBookedHotels($userId, $limit = 10)
    {
        try {
            $sql = "SELECT 
                        h.id AS hotel_id, 
                        h.name AS hotel_name, 
                        h.address AS hotel_address, 
                        COUNT(DISTINCT bi.room_id) AS room_count,
                        b.created_at AS booking_date,
                        b.id AS booking_id,
                        b.payment_status,
                        b.payment_method
                    FROM bookings b
                    JOIN booking_items bi ON b.id = bi.booking_id
                    JOIN rooms r ON bi.room_id = r.id
                    JOIN hotels h ON r.hotel_id = h.id
                    WHERE b.user_id = ?
                    GROUP BY h.id, b.id
                    ORDER BY b.created_at DESC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $hotels = $stmt->fetchAll();

            return $hotels;
        } catch (\PDOException $e) {
            $this->errors[] = "Lỗi khi lấy danh sách khách sạn: " . $e->getMessage();
            return [];
        }
    }

    public function cancelBooking($bookingId)
    {
        $this->db->beginTransaction();
        try {
            // Cập nhật trạng thái booking
            $sql = "UPDATE bookings SET payment_status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([self::PAYMENT_CANCELLED, $bookingId]);

            // Cập nhật trạng thái booking items
            $sql = "UPDATE booking_items SET booking_status = ?, updated_at = NOW() WHERE booking_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([self::STATUS_CANCELLED, $bookingId]);

            // Cập nhật trạng thái phòng
            $sql = "SELECT room_id FROM booking_items WHERE booking_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$bookingId]);
            $roomIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($roomIds)) {
                foreach ($roomIds as $roomId) {
                    $roomModel = new Room();
                    $roomModel->updateStatus(Room::STATUS_AVAILABLE, $roomId);
                }
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
