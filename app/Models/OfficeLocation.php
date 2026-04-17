<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $table = 'tb_office_location';

    protected $fillable = [
        'office_id',
        'latitude',
        'longitude',
        'radius'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
