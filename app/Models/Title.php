<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    use HasFactory;

    protected $fillable = ['imdb_id', 'thumb', 'poster', 'title', 'description', 'rate', 'start_year', 'end_year', 'type'];

    public function genres() {
        return $this->belongsToMany(Genre::class, 'title_genre');
    }

    public function imdbLink() {
        return sprintf('https://www.imdb.com/title/tt%s/', $this->imdb_id);
    }

    public function isSeries() {
        return $this->type === 'series';
    }

    public function getYear() {
        return $this->isSeries()
            ? sprintf('%s - %s', $this->start_year, $this->end_year ?? 'present')
            : $this->start_year;
    }

    public function lists() {
        return $this->belongsToMany(UserList::class, 'title_user-list');
    }
}
