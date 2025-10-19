<?php

namespace app\Models;

use core\BaseModel;

class Amenity extends BaseModel
{
    // Định nghĩa tên bảng
    protected $table = 'amenities';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status'
    ];

    protected $rules = [
        'name' => 'required|max:100',
        'description' => 'required|max:255',
        'icon' => 'required|max:100',
        'status' => 'required|in:active,inactive'
    ];

    /**
     * Lấy danh sách tiện nghi theo trạng thái
     * @param string $status Trạng thái của tiện nghi (active/inactive)
     * @return array Danh sách tiện nghi
     */
}
