<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateTitle;
use App\Models\Title;
use App\Models\UserList;
use App\Notifications\TitlesSuggestions;
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
            'description' => ['nullable']
        ]);

        $list = auth()->user()->lists()->create($request->all());

        return redirect()->route('user-lists.show', $list);
    }

    public function show(UserList $list)
    {
        $list = $list->load('titles');

        return view('user-lists.show', compact('list'));
    }

    public function suggest(Request $request)
    {
        $request->validate([
            'user_list' => ['required_without_all:title'],
            'title' => ['required_without_all:user_list'],
            'remove_existing_titles' => ['required_if:user_list,true'],
        ]);

        $titles = collect();
        if ($request->has('title'))
        {
            $titles->add(Title::find($request->get('title')));
        }
        if ($request->has('user_list'))
        {
            $userList = UserList::find($request->get('user_list'));
            $titles = $userList->titles;
        }


        $suggested_titles = collect();
        $titles->each(function (Title $title) use (&$suggested_titles)
        {
            $suggested_titles = $suggested_titles->merge($title->recommendations);
        });
        $suggested_titles = $suggested_titles->unique('imdb_id')->values();

        $suggested_titles->each(function (Title $title)
        {
            UpdateTitle::dispatch($title);
        });

        $suggested_titles = Title::find(
            $suggested_titles
                ->pluck('id')
                ->values()
                ->toArray()
        );

        $request
            ->user()
            ->notify(new TitlesSuggestions($suggested_titles));

        return back()->with('success', 'We will notify you the suggestions!');
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
