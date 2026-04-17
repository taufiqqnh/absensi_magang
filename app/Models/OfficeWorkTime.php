<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeWorkTime extends Model
{
    protected $table = 'tb_office_work_times';

    protected $fillable = [
        'office_id',
        'check_in_time',
        'check_out_time',
        'late_tolerance',
        'early_leave_tolerance'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
