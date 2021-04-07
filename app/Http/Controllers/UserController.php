<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verified']);
    }

    public function profile(Request $request)
    {
        return view('user.profile');
    }

    public function suggestions(Request $request)
    {
        $suggestions = $request->user()->suggestions->sortByDesc('created_at');
        return view('user.suggestions', compact('suggestions'));
    }

    public function suggestion(Request $request, Suggestion $suggestion)
    {
        dd($suggestion);
    }
}
