<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    public const VERSION = 1;

    protected $fillable = [
        'user_id',
        'titles',
        'recommendations',
        'type',
        'version'
    ];

    protected $casts = [
        'titles'          => AsCollection::class,
        'recommendations' => AsCollection::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeAttribute($type)
    {
        switch ($type){
            case 'title':
                return 'Title';
            case 'user_list':
                return 'User list';
            default:
                return 'Unknown';
        }
    }
}
