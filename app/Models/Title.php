<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Title extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id', 'thumb', 'poster', 'title', 'description', 'rate', 'start_year', 'end_year', 'type'
    ];

    public function imdbLink(): string
    {
        return sprintf('https://www.imdb.com/title/tt%s/', $this->imdb_id);
    }

    public function isSeries(): bool
    {
        return $this->type === 'series';
    }

    public function getYear(): string
    {
        return $this->isSeries()
            ? sprintf('%s - %s', $this->start_year, $this->end_year ?? 'present')
            : $this->start_year;
    }

    public function getThumbAttribute($thumb): string
    {
        $query = http_build_query(['text' => $this->title]);
        return empty($thumb) ? 'https://via.placeholder.com/261x384.png?'.$query : $thumb;
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'title_genre');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(UserList::class, 'title_user-list');
    }
}
