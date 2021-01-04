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

        $titles_to_add = $this->suggestUserTitlesToAdd($list);

        return view('user-lists.show', compact('list', 'titles_to_add'));
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

        $suggested_titles = $suggested_titles
            ->whereNotIn('id', $list_titles_ids)
            ->get();

        dd($suggested_titles);
    }

    private function suggestUserTitlesToAdd(UserList $list)
    {
        $titles_to_add = collect();

        $this_list_titles = $list->titles;
        $all_lists = UserList::with('titles')->whereNotIn('id',
            auth()->user()->lists()->inRandomOrder()->limit(40)->get()->pluck('id'))->get();

        $_sorted_lists = [];
        $collect_suggestions = false;
        foreach ($all_lists as $_list) {
            $intersect = $_list->titles->intersect($this_list_titles)->pluck('id')->toArray();
            $difference = $_list->titles->whereNotIn('imdb_id',
                $_list->titles->intersect($this_list_titles)->pluck('imdb_id'))->pluck('id')->toArray();

            if (!$collect_suggestions) {
                $collect_suggestions = count($difference) > 0;
            }

            $_sorted_lists[] = compact('intersect', 'difference');
        }

        if ($collect_suggestions) {
            usort($_sorted_lists, function ($a, $b) {
                return (count($b['intersect']) - count($a['intersect']));
            });

            foreach ($_sorted_lists as $sorted_list) {
                $titles_to_add = $titles_to_add->merge($sorted_list['difference']);
            }

            $titles_to_add = $titles_to_add->unique();

        } else {
            $titles_to_add = Title::whereNotIn('id',
                $list->titles->pluck('id')->toArray())->inRandomOrder()->limit(5)->pluck('id')->toArray();
        }

        $titles_to_add = Title::find($titles_to_add);

        return $titles_to_add;
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
