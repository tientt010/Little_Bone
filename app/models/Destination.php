<?php

namespace app\Models;

use core\BaseModel;

class Destination extends BaseModel
{
    protected $table = 'travel_destinations';

    protected $fillable = [
        'name',
        'address',
        'description',
        'image'
    ];


    protected $rules = [
        'name' => 'required|max:100',
        'address' => 'required',
        'description' => 'required'
    ];

    public function getPopularDestinations($limit = 6)
    {
        try {
            $limit = (int) $limit;
            $checkTable = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if ($checkTable->rowCount() === 0) {

                return [];
            }

            $sql = "SELECT d.*, COUNT(h.id) AS hotel_count
                    FROM {$this->table} d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    WHERE h.id IS NOT NULL
                    GROUP BY d.id
                    ORDER BY d.id
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            if (empty($results)) {
            }



            return $results;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Lấy danh sách địa điểm du lịch theo khu vực
    public function getDestinationsByRegion($region, $limit = 6)
    {
        try {
            $limit = (int) $limit;

            $sql = "SELECT d.*, COUNT(h.id) AS hotel_count
                    FROM {$this->table} d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    WHERE d.region = ?
                    GROUP BY d.id
                    ORDER BY d.id ASC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$region]);
            $results = $stmt->fetchAll();


            return $results;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Lấy tất cả địa điểm du lịch kèm số lượng khách sạn
    public function getAllDestinations()
    {
        try {

            $sql = "SELECT d.*, COUNT(h.id) AS hotel_count
                    FROM {$this->table} d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    GROUP BY d.id
                    ORDER BY d.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as &$destination) {
                if (empty($destination['image'])) {
                    $destination['image'] = 'default.jpg';
                }
            }


            return $results;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Tìm kiếm địa điểm du lịch theo tên
    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $name . '%']);
        return $stmt->fetchAll();
    }

    // Lấy thông tin chi tiết của một địa điểm du lịch
    public function getDetails($id)
    {
        $destination = $this->find($id);
        if (!$destination) {
            return false;
        }
        $sql = "SELECT h.id, h.name, h.star_rating, h.image, h.address,
                MIN(r.price_per_night) AS min_price
                FROM hotels h
                LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
                WHERE h.destination_id = ? AND h.status = 'active'
                GROUP BY h.id
                ORDER BY h.star_rating DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $destination['hotels'] = $stmt->fetchAll();

        $destination['images'] = $this->getDestinationImages($id);

        return $destination;
    }

    // Lấy danh sách hình ảnh của địa điểm du lịch
    public function getDestinationImages($destinationId)
    {
        $sql = "SELECT * FROM images 
                WHERE entity_type = 'destination' AND entity_id = ?
                ORDER BY is_primary DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$destinationId]);
        return $stmt->fetchAll();
    }

    // Đếm số lượng khách sạn trong một địa điểm du lịch
    public function countHotels($destinationId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM hotels 
                WHERE destination_id = ? AND status = 'active'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$destinationId]);
        $result = $stmt->fetch();

        return $result['count'] ?? 0;
    }

    // searchDestinations
    public function searchDestinations($filters = [])
    {
        $sql = "SELECT d.*, COUNT(h.id) AS hotel_count 
                FROM {$this->table} d
                LEFT JOIN hotels h ON d.id = h.destination_id AND h.status = 'active'
                WHERE 1=1";

        $params = [];
        if (!empty($filters['name'])) {
            $sql .= " AND d.name LIKE ?";
            $params[] = '%' . $filters['name'] . '%';
        }

        if (!empty($filters['address'])) {
            $sql .= " AND d.address LIKE ?";
            $params[] = '%' . $filters['address'] . '%';
        }

        $sql .= " GROUP BY d.id ORDER BY hotel_count DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy danh sách điểm đến được sắp xếp theo ID
    public function getDestinationsSortedById($limit = 5)
    {
        try {
            $limit = (int) $limit;

            $sql = "SELECT d.*, COUNT(h.id) AS hotel_count
                    FROM {$this->table} d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    GROUP BY d.id
                    ORDER BY d.id ASC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();


            return $results;
        } catch (\PDOException $e) {

            return [];
        }
    }

    // Lấy danh sách điểm đến ngoại trừ một khu vực cụ thể
    public function getDestinationsExcludeRegion($excludeRegion, $limit = 6)
    {
        try {
            $limit = (int) $limit;

            $sql = "SELECT d.*, COUNT(h.id) as hotel_count
                    FROM travel_destinations d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    WHERE d.region != ?
                    GROUP BY d.id
                    ORDER BY d.featured DESC, hotel_count DESC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$excludeRegion]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {

            return [];
        }
    }

    // Lấy danh sách điểm đến nước ngoài
    public function getForeignDestinations($limit = 3)
    {
        try {
            $limit = (int) $limit;

            $sql = "SELECT d.*, COUNT(h.id) AS hotel_count
                    FROM {$this->table} d
                    LEFT JOIN hotels h ON d.id = h.destination_id
                    WHERE d.region != 'Việt Nam'
                    GROUP BY d.id
                    ORDER BY d.id ASC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {

            return [];
        }
    }
}
