<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'creation_date',
        'description',
        'tags',
        'general_category',
        'image',
        'file',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function isRatedByUser($userId)
    {
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    public function getRatingByUser($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
    }
}
