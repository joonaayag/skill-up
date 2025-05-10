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
        'sector_category',
        'image',
        'link',
    ];
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'school_project_id');
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

    public function comments()
    {
        return $this->hasMany(Comment::class, 'school_project_id');
    }

    public function mainComments()
    {
        return $this->comments()->whereNull('parent_id');
    }
}