<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    use HasFactory;
    protected $table = 'admin_users';
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'dob',
        'phone',
        'address'
    ];

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function forgot_passwords() {
        return $this->hasMany(ForgotPassword::class);
    }
}
