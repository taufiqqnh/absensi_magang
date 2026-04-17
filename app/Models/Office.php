<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'tb_office';

    protected $fillable = [
        'office_name',
        'address'
    ];

    public function location()
    {
        return $this->hasOne(OfficeLocation::class);
    }

    public function workTime()
    {
        return $this->hasOne(OfficeWorkTime::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
