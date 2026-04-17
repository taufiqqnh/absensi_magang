<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceData extends Model
{
    protected $table = 'tb_face_data';

    protected $fillable = [
        'id_users',
        'face_descriptor',
        'face_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
