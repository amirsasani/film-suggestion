<?php


namespace App\Services\Imdb;


use App\Jobs\UpdateTitle;
use App\Models\Genre;
use App\Models\Title;
use Illuminate\Support\Collection;
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
            return Handler::insertTitle($seriesImdbId);
        }

        $imdb_id = $imdb->imdbid();

        $thumb = $imdb->photo();
        $poster = $imdb->photo(false);
        $title = $imdb->title();
        $rate = $imdb->rating();
        $start_year = $imdb->yearspan()['start'];
        $end_year = $imdb->yearspan()['end'] != 0 ? $imdb->yearspan()['end'] : null;
        $type = $imdb->is_serial() ? 'series' : 'movie';


        $imdb_id = Handler::prepareImdbId($imdb_id);

        if(empty($rate)){
            $rate = 0;
        }

        $_title = Title::updateOrCreate(compact('imdb_id'), compact(
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
        $_title->genres()->sync($genres);

        $recommendations = [];
        foreach ($imdb->movie_recommendations() as $suggestion)
        {
            $imdb_id = Handler::prepareImdbId($suggestion['imdbid']);
            $title = Title::updateOrCreate(compact('imdb_id'));

            $recommendations[] = $title->id;
        }
        $_title->recommendations()->sync($recommendations);

        return $_title;
    }

    public static function prepareImdbId($imdb_id)
    {
        return sprintf('%020d', $imdb_id);
    }
}
