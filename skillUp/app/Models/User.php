<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role',
        'avatar',
        'cv',
        'foto_perfil',
        'banner'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'author_id');
    }

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'company_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function schoolProjects()
    {
        return $this->hasMany(SchoolProject::class);
    }
public function notifications()
{
    return $this->hasMany(Notification::class);
}
}
