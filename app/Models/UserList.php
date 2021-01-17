<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    use HasFactory;

    protected $table = 'user_lists';

    protected $fillable = ['title', 'description', 'user_id'];

    public function user() {
        return $this->hasOne(User::class, 'user_id');
    }

    public function titles() {
        return $this->belongsToMany(Title::class, 'title_user-list');
    }
}
