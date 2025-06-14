<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subtitle',
        'description',
        'sector_category',
        'general_category',
        'state',
        'company_id',
        'logo',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'offer_id');
    }

    public function hasApplied($userId)
    {
        return $this->applications()->where('user_id', $userId)->exists();
    }
    protected static function booted()
    {
        static::deleting(function ($jobOffer) {
            Favorite::where('type', 'oferta')->where('reference_id', $jobOffer->id)->delete();
        });
    }

}
