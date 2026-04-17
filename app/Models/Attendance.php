<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tb_attendances';

    protected $fillable = [
        'id_users',
        'office_id',
        'device_id',
        'latitude',
        'longitude',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'description',
        'image',
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    // relasi ke office
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    // relasi ke device
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
