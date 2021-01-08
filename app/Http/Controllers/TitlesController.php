<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Title;
use App\Services\Imdb\Handler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\Promise\all;

class TitlesController extends Controller
{
    public function index(Request $request)
    {

        $request->validate([
            'start_year' => ['digits:4', 'nullable'],
            'end_year' => ['digits:4', 'nullable'],
            'type' => ['in:series,movie', 'nullable'],
            'rate' => ['numeric', 'nullable'],
            'genres' => ['exists:genres,id', 'nullable'],
        ]);

        $titles = Title::with(['genres']);

        $genres = $this->prepareGenresForIndex();

        $start_years = $titles->pluck('start_year')->unique()->sortDesc()->reject(function ($year) {
            return empty($year);
        });
        $end_years = $titles->pluck('end_year')->unique()->sortDesc()->reject(function ($year) {
            return empty($year);
        });

        $selected = [];
        $titles = $this->filterTitles($request, $titles, $selected);
        $titles = $titles->orderByDesc('updated_at');
        $titles = $titles->paginate(12);

        $user_lists = [];
        if (auth()->user()) {
            $user_lists = auth()->user()->lists;
        }

        return view('titles.list', compact('titles', 'genres', 'start_years', 'end_years', 'selected', 'user_lists'));
    }

    private function filterTitles(Request $request, Builder $titles, &$selected)
    {
        if ($request->query('search')) {
            $titles = $titles->where('title', 'like', '%'.$request->query('search').'%');
            $selected['search'] = $request->query('search');
        }
        if ($request->query('start_year')) {
            $titles = $titles->where('start_year', '>=', $request->query('start_year'));
            $selected['start_year'] = $request->query('start_year');
        }
        if ($request->query('end_year')) {
            $titles = $titles->where('end_year', '<=', $request->query('end_year'));
            $selected['end_year'] = $request->query('end_year');
        }
        if ($request->query('type')) {
            $titles = $titles->where('type', '=', $request->query('type'));
            $selected['type'] = $request->query('type');
        }
        if ($request->query('rate')) {
            $titles = $titles->where('rate', '>=', $request->query('rate'));
            $selected['rate'] = $request->query('rate');
        }
        if ($request->query('genres')) {
            $titles = $titles->whereHas('genres', function (Builder $query) use ($request) {
                $query->where('id', '=', $request->query('genres'));
            });
            $selected['genre'] = $request->query('genres');
        }

        return $titles;
    }

    private function prepareGenresForIndex()
    {
        $output = [];
        $genres = Genre::all();
        foreach ($genres as $genre) {
            $output[$genre->id] = $genre->title;
        }
        return $output;
    }

    public function insertForm()
    {
        return view('titles.insert');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'imdb_id' => ['required']
        ]);

        DB::beginTransaction();
        try {

            $title = Handler::insertTitle($request->get('imdb_id'));

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        return redirect()->route('titles.index');
    }

    public function imdbDatasetPopulate(Request $request)
    {
        $disk = Storage::disk('local');

        $dataset_file_name = "title.ratings.tsv.gz";
        $dataset_file_path = storage_path('app/');
        $dataset_file_path .= $dataset_file_name;

        $should_download_dataset = true;
        if ($disk->exists($dataset_file_name)) {
            $dataset_file_last_downloaded_time = Carbon::createFromTimestamp($disk->lastModified($dataset_file_name));
            $should_download_dataset = $dataset_file_last_downloaded_time->subHours(23) >= Carbon::now();
        }

        if ($should_download_dataset) {
            $contents = file_get_contents("https://datasets.imdbws.com/".$dataset_file_name);
            $disk->put($dataset_file_name, $contents);
        }

        $input = gzopen($dataset_file_path, 'r');

        $i = 0;
        while ($row = fgetcsv($input, 0, "\t")) {
            if ($i > 0) {
                $row[] = $i;
                $id = $row[0];

                $title = Handler::insertTitle($id);

                $log_msg = sprintf('title #%s added', $title->id);
                Log::info($log_msg);
            }
            $i++;
        }
    }
}
