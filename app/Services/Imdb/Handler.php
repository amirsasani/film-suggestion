<?php


namespace App\Services\Imdb;


use App\Models\Genre;
use App\Models\Title;
use Imdb\Config;
use Imdb\Title as ImdbTitle;
use Symfony\Component\Cache\Psr16Cache;

class Handler
{
    public static function insertTitle(string $imdb_id)
    {

        $config = new Config();
        $config->language = 'en';
        $config->cache_expire = 86400;

        $logger = null;
        $pool = null;

        try
        {
            $pool = app('cache.psr6');
            $pool = new Psr16Cache($pool);
        } catch (\Exception $e)
        {
            $pool = null;
        }

        $imdb = new ImdbTitle($imdb_id, $config, $logger, $pool);

        $movieType = $imdb->movietype();
        if (stripos($movieType, 'episode') != false)
        {
            $episodeDetails = $imdb->get_episode_details();
            $seriesImdbId = $episodeDetails['imdbid'];
            return Title::where('imdb_id', '=', $seriesImdbId)->first();
        }

        $imdb_id = $imdb->imdbid();

        $thumb = $imdb->photo();
        $poster = $imdb->photo(false);
        $title = $imdb->title();
        $rate = $imdb->rating();
        $start_year = $imdb->yearspan()['start'];
        $end_year = $imdb->yearspan()['end'] != 0 ? $imdb->yearspan()['end'] : null;
        $type = $imdb->is_serial() ? 'series' : 'movie';

        if (!empty($imdb->orig_title()))
        {
            $title = $imdb->orig_title();
        }

        $title = Title::firstOrCreate(compact(
            'imdb_id',
            'thumb',
            'poster',
            'title',
            'rate',
            'start_year',
            'end_year',
            'type'
        ));

        $genres = [];
        foreach ($imdb->genres() as $imdb_genre)
        {
            $genre = Genre::firstOrCreate(['title' => $imdb_genre]);
            $genres[] = $genre->id;
        }
        $title->genres()->sync($genres);

        return $title;
    }
}
