<?php


namespace App\Services\Imdb;


use App\Models\Genre;
use App\Models\Title;

class Handler
{
    public static function insertTitle(\Imdb\Title $imdb) {
        $title = Title::firstOrCreate(['imdb_id' => $imdb->imdbid()], [
            'thumb' => $imdb->photo(),
            'poster' => $imdb->photo(false),
            'title' => !empty($imdb->orig_title()) ? $imdb->orig_title() : $imdb->title(),
            'rate' => $imdb->rating(),
            'start_year' => $imdb->yearspan()['start'],
            'end_year' => $imdb->yearspan()['end'] != 0 ? $imdb->yearspan()['end'] : null,
            'type' => $imdb->is_serial() ? 'series' : 'movie'
        ]);

        $genres = [];
        foreach ($imdb->genres() as $imdb_genre) {
            $genre = Genre::firstOrCreate(['title' => $imdb_genre]);
            $genres[] = $genre->id;
        }
        $title->genres()->sync($genres);

        return $title;
    }
}
