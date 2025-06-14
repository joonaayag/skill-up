<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
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
        return $this->hasMany(Rating::class, 'project_id');
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
        return $this->hasMany(Comment::class);
    }

    public function mainComments()
    {
        return $this->comments()->whereNull('parent_id');
    }
    protected static function booted()
    {
        static::deleting(function ($project) {
            Favorite::where('type', 'proyecto')->where('reference_id', $project->id)->delete();
        });
    }

}