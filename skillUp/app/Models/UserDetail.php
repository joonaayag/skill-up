<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'current_course',
        'specialization',
        'educational_center',
        'department',
        'validation_document',
        'cif',
        'address',
        'sector',
        'website',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
