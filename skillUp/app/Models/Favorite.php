<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'reference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return match($this->type) {
            'proyecto' => Project::find($this->reference_id),
            'oferta' => JobOffer::find($this->reference_id),
            default => null
        };
    }

}

