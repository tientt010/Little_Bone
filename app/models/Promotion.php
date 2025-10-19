<?php

namespace app\Models;

use core\BaseModel;

class Promotion extends BaseModel
{
    protected $table = 'promotions';

    // Các trường có thể được điền
    public function getAvailablePromotions($hotelId)
    {
        $currentDate = date('Y-m-d');

        $sql = "SELECT * FROM promotions 
                WHERE (hotel_id IS NULL OR hotel_id = :hotel_id)
                AND start_date <= :current_date 
                AND end_date >= :current_date
                AND is_active = 1
                ORDER BY discount_value DESC";

        $result = $this->db->query($sql, [
            'hotel_id' => $hotelId,
            'current_date' => $currentDate
        ]);

        return $result;
    }

    // Lấy danh sách các khuyến mãi đã áp dụng cho một khách sạn cụ thể
    public function getAppliedPromotions($hotelId)
    {
        $currentDate = date('Y-m-d');

        try {
            $sql = "SELECT p.* FROM promotions p 
                    JOIN hotel_promotions hp ON p.id = hp.promotion_id 
                    WHERE hp.hotel_id = :hotel_id
                    AND p.start_date <= :current_date 
                    AND p.end_date >= :current_date
                    AND p.status = 'active'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'hotel_id' => $hotelId,
                'current_date' => $currentDate
            ]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {

            return [];
        }

        return $result;
    }

    // cập nhât khuyến mãi cho khách sạn
    public function updateHotelPromotions($hotelId, $promotionIds)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM hotel_promotions WHERE hotel_id = :hotel_id");
            $stmt->execute(['hotel_id' => $hotelId]);

            if (!empty($promotionIds)) {
                $insertStmt = $this->db->prepare(
                    "INSERT INTO hotel_promotions (hotel_id, promotion_id) VALUES (:hotel_id, :promotion_id)"
                );

                foreach ($promotionIds as $promotionId) {
                    $insertStmt->execute([
                        'hotel_id' => $hotelId,
                        'promotion_id' => $promotionId
                    ]);
                }
            }

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();

            return false;
        }
    }


    // Lấy danh sách khách sạn áp dụng khuyến mãi
    public function getAppliedHotels($promotionId, $activeOnly = true)
    {
        try {
            $statusCondition = $activeOnly ? "AND h.status = 'active'" : "";

            $sql = "SELECT h.* FROM hotels h
                    JOIN hotel_promotions hp ON h.id = hp.hotel_id
                    WHERE hp.promotion_id = :promotion_id
                    $statusCondition
                    ORDER BY h.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['promotion_id' => $promotionId]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\Exception $e) {

            return [];
        }
    }


    public function isValid($id)
    {
        $sql = "SELECT * FROM promotions WHERE id = :id AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return !empty($result);
    }
}
