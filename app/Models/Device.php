<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'tb_devices';

    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
