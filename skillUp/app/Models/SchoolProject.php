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

}
