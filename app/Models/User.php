<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Models\Brand;

class User extends Authenticatable implements MustVerifyEmail
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'username',
    'email',
    'password',
    'profile_image',
    'bio',
    'instagram',
    'user_location',
  ];

  public function brands()
  {
    return $this->hasMany(Brand::class);
  }

  public function votedBrands() 
  {
    return $this->belongsToMany(Brand::class, 'brand_votes')->withPivot('vote')->withTimestamps();
  }

  public function savedBrands() 
  {
    return $this->belongsToMany(Brand::class, 'brand_saves')->withTimestamps();
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  public function likedComments()
  {
    return $this->hasMany(CommentLike::class);
  }

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }
}
