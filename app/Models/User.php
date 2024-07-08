<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
        //     'name',
        //     'email',
        //     'password',
        // ];
    protected $guarded=[];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function Prase()
    {
        return $this->belongsTo(Praise::class);
    }
    public function Country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function State()
    {
        return $this->belongsTo(State::class,'state_id','id');
    }
    public function city()
    {
        return $this->belongsTo(City::class,'city_id','id');
    }

    public function praises()
    {
        return $this->hasMany(Praise::class, 'receiver_id', 'id');
    }
}
