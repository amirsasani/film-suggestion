<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserListController extends Controller
{
    public function index()
    {
        $lists = auth()->user()->lists()->paginate(10);

        return view('user-lists.list', compact('lists'));
    }

    public function insertForm()
    {
        return view('user-lists.insert');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'description' => ['required']
        ]);

        $list = auth()->user()->lists()->create($request->all());

        return redirect()->route('user-lists.show', $list);
    }

    public function show(UserList $list)
    {
        $list = $list->load('titles');

        return view('user-lists.show', compact('list'));
    }

    public function suggest(UserList $list)
    {
        $list_titles = $list->titles;
        $list_titles_ids = $list_titles->pluck('id')->toArray();


        $list_titles_genres = $list_titles
            ->pluck('genres.*')
            ->flatten()
            ->keyBy('id')
            ->sortDesc()
            ->take(3)
            ->keys()
            ->all();
        $suggested_titles = Title::with('genres')->whereHas('genres',
            function (Builder $builder) use ($list_titles_genres) {
                $builder->whereIn('id', $list_titles_genres);
            });

        $list_titles_average_rating = $list_titles->avg('rate');
        $suggested_titles = $suggested_titles->where('rate', '>=', $list_titles_average_rating);

        $list_titles_median_start_year = $list_titles->pluck('start_year')->sort()->toArray();
        $list_titles_median_start_year = $this->remove_outliers($list_titles_median_start_year);
        $list_titles_median_start_year = collect($list_titles_median_start_year);
        $list_titles_median_start_year = $list_titles_median_start_year->min();
        $list_titles_median_start_year = floor($list_titles_median_start_year);
        $list_titles_median_start_year = intval($list_titles_median_start_year);


        $list_titles_median_end_year = $list_titles->pluck('end_year')->sort()->toArray();
        $list_titles_median_end_year = $this->remove_outliers($list_titles_median_end_year);
        $list_titles_median_end_year = collect($list_titles_median_end_year);
        $list_titles_median_end_year = $list_titles_median_end_year->max();
        $list_titles_median_end_year = floor($list_titles_median_end_year);
        $list_titles_median_end_year = intval($list_titles_median_end_year);

//        $suggested_titles = $suggested_titles->whereIn('start_year', [$list_titles_median_start_year, $list_titles_median_end_year]);

        $list_titles_mode_types = $list_titles->mode('type');
        $suggested_titles = $suggested_titles->whereIn('type', $list_titles_mode_types);


        $suggested_titles = $suggested_titles
            ->whereNotIn('id', $list_titles_ids)
            ->take(24)
            ->paginate(6);

        return view('user-lists.suggestions', ['titles' => $suggested_titles, 'list' => $list]);
    }

    function remove_outliers($dataset, $magnitude = 1)
    {

        $count = count($dataset);
        $mean = array_sum($dataset) / $count; // Calculate the mean
        $deviation = sqrt(array_sum(array_map([$this, 'sd_square'], $dataset, array_fill(0, $count,
                    $mean))) / $count) * $magnitude; // Calculate standard deviation and times by magnitude

        return array_filter($dataset, function ($x) use ($mean, $deviation) {
            return ($x <= $mean + $deviation && $x >= $mean - $deviation);
        }); // Return filtered array of values that lie within $mean +- $deviation.
    }

    function sd_square($x, $mean)
    {
        return pow($x - $mean, 2);
    }

    public function addTitleToList(Request $request, UserList $list, Title $title)
    {
        $list->titles()->syncWithoutDetaching($title);

        return back();
    }

    public function removeTitleFromList(Request $request, UserList $list, Title $title)
    {
        $list->titles()->detach([$title->id]);

        return back();
    }
}
