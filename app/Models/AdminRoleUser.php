<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRoleUser extends Model
{
    use HasFactory;

    protected $table = 'admin_role_users';
    protected $fillable = [
        'role_id',
        'user_id'
    ];
}
