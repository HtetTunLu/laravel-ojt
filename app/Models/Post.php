<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'admin_user_id',
    ];
    public function admin_user() {
        return $this->belongsTo(AdminUser::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function ($request) {
            $post = Post::where('id', $request->id)->first();
            $post->deleted_user_id = \Illuminate\Support\Facades\Auth::user()->id;
            $post->save();
        });
    }
}
