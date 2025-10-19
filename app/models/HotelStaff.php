<?php

namespace app\Models;

use core\BaseModel;
use app\Models\Hotel;

class HotelStaff extends Hotel
{
    protected $table = 'hotel_staff';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'role',
        'assigned_date'
    ];

    public function getHotelByUserId($userId)
    {
        $sql = "SELECT hs.hotel_id, h.name as hotel_name 
                FROM hotel_staff hs
                JOIN hotels h ON hs.hotel_id = h.id
                WHERE hs.user_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
