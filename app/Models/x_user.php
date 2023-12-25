<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class XUsers extends Authenticatable implements JWTSubject
{
    use HasFactory, HasFactory, Notifiable;
    protected $primaryKey = 'user_id';

    public $incrementing = false;
    protected $table    = "x_users";
    protected $fillable = [
        'user_id',
        'username',
        'full_name',
        'email',
        'phone_number',
        'password',
        'token',
        'is_login',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password_jwt'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    // public function setPasswordAttribute($value) {
    //     $this->attributes['password'] = Hash::make($value);
    // }

    public function products()
    {
        return $this->hasMany(Products::class);
    }


}