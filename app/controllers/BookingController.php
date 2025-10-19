<?php

namespace app\Controllers;

use core\BaseController;
use app\Models\Booking;
use app\Models\Room;
use app\Models\Hotel;
use app\Models\User;

class BookingController extends BaseController
{
    private $bookingModel;
    private $roomModel;
    private $hotelModel;
    private $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->hotelModel = new Hotel();
    }

    // Hiển thị trang giỏ hàng
    public function cart()
    {
        $cartData = [];
        $totalRooms = 0;
        $totalPrice = 0;

        if ($this->isPost()) {
            $rooms = $this->getPost('rooms');

            if (is_array($rooms) && !empty($rooms)) {

                foreach ($rooms as $roomId => $qty) {
                    $qty = intval($qty);
                    if ($qty <= 0) continue;

                    $room = $this->roomModel->getRoomById($roomId);
                    if (!$room) continue;

                    $hotel = $this->hotelModel->getHotelById($room['hotel_id']);
                    if (!$hotel) continue;

                    $checkIn = $this->getPost("check_in")[$roomId] ?? date('Y-m-d');
                    $checkOut = $this->getPost("check_out")[$roomId] ?? date('Y-m-d', strtotime('+1 day'));

                    $nights = max(1, (strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24));

                    $roomPrice = $room['price'] * $qty * $nights;

                    $totalRooms += $qty;
                    $totalPrice += $roomPrice;

                    $hotelId = $room['hotel_id'];
                    if (!isset($cartData[$hotelId])) {
                        $cartData[$hotelId] = [
                            'id' => $hotelId,
                            'name' => $hotel['name'] ?? 'Khách sạn không xác định',
                            'address' => $hotel['address'] ?? '',
                            'rooms' => [],
                            'total' => 0
                        ];
                    }

                    $cartData[$hotelId]['rooms'][$roomId] = [
                        'id' => $roomId,
                        'name' => $room['name'] ?? 'Phòng không xác định',
                        'room_type' => $room['room_type'] ?? 'standard',
                        'price' => $room['price'] ?? 0,
                        'quantity' => $qty,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'nights' => $nights,
                        'total_price' => $roomPrice
                    ];

                    $cartData[$hotelId]['total'] += $roomPrice;
                }
            }
        }
        $this->view('booking/cart', [
            'cartData' => $cartData,
            'totalRooms' => $totalRooms,
            'totalPrice' => $totalPrice
        ]);
    }

    // Hiển thị trang thanh toán
    public function checkout()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login?redirect=/booking/cart');
            return;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);

        if (!isset($_POST['cart_data'])) {
            $this->redirect('/booking/cart');
            return;
        }

        $this->view('booking/checkout', [
            'user' => $user,
            'title' => 'Thanh Toán'
        ]);
    }

    // Xử lý đặt phòng
    public function process()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Bạn cần đăng nhập để đặt phòng']);
            return;
        }

        if (!$this->isAjax()) {
            $this->jsonResponse(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
            return;
        }

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (!$data || !isset($data['cart_data']) || empty($data['cart_data']['hotels'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu đặt phòng không hợp lệ']);
            return;
        }

        // Chuẩn bị dữ liệu đặt phòng
        $userId = $_SESSION['user_id'];
        $cartData = $data['cart_data'];

        $paymentMethod = $data['payment_method'] === 'pay_now' ? 'credit_card' : 'at_hotel';
        $paymentStatus = $paymentMethod === 'credit_card' ? 'paid' : 'pending';

        $bookingData = [
            'user_id' => $userId,
            'total_price' => $cartData['originalTotal'],
            'discount' => $cartData['totalDiscount'],
            'taxes' => $cartData['taxes'],
            'final_amount' => $cartData['finalTotal'],
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'],
            'notes' => $data['customer_notes'] ?? ''
        ];

        $bookingItems = [];

        foreach ($cartData['hotels'] as $hotel) {
            foreach ($hotel['rooms'] as $room) {
                // Tính số đêm
                $checkInDate = $room['checkInDate'];
                $checkOutDate = $room['checkOutDate'];
                $nights = isset($room['nights']) ? $room['nights'] :
                    $this->calculateNights($checkInDate, $checkOutDate);

                $bookingItems[] = [
                    'room_id' => $room['id'],
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'booking_status' => 'pending',
                    'unit_price' => $room['price'],
                    'nights' => $nights,
                    'total_price' => $room['price'] * $room['quantity'] * $nights,
                ];
            }
        }

        $result = $this->bookingModel->createBookingWithItems($bookingData, $bookingItems);

        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Đặt phòng thành công!',
                'booking_id' => $result
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Không thể hoàn tất đặt phòng. ' . implode(', ', $this->bookingModel->getErrors())
            ]);
        }
    }

    // Kiểm tra xem yêu cầu có phải là AJAX hay không
    private function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    // Tính số đêm giữa ngày nhận phòng và ngày trả phòng
    private function calculateNights($checkInDate, $checkOutDate)
    {
        $checkIn = new \DateTime($checkInDate);
        $checkOut = new \DateTime($checkOutDate);
        $interval = $checkIn->diff($checkOut);
        return $interval->days > 0 ? $interval->days : 1;
    }
}
