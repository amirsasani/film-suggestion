<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\UserList;
use Illuminate\Http\Request;

class UserListController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verified']);
    }

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
            'title'       => ['required'],
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
