<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Title extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',
        'thumb',
        'poster',
        'title',
        'description',
        'rate',
        'start_year',
        'end_year',
        'type'
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
        $start_year = $this->start_year ?? '';
        $end_year = $this->end_year ?? 'present';

        if($this->isSeries()){
            return sprintf('%s - %s', $start_year, $end_year);
        }
        return $start_year;
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

    public function recommendations(): BelongsToMany
    {
        return $this->belongsToMany(
            Title::class,
            'recommendations',
            'recommendation_id',
            'title_id',
        )->withTimestamps();
    }

    public function scopeNoData(Builder $query)
    {
        return $query->whereNull('type');
    }

    public function scopeNeedUpdate(Builder $query)
    {
        $date = Carbon::now()->subDays(7);
        return $query->where('populated_at', '<=', $date);
    }

    public function scopeToShow(Builder $query)
    {
        return $query->whereNotNull('type');
    }
}
