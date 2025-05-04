<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'author_id',
        'tags',
        'general_category',
        'link',
        'creation_date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
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
