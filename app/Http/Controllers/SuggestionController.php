<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateTitle;
use App\Models\Suggestion;
use App\Models\Title;
use App\Models\UserList;
use App\Notifications\TitlesSuggestions;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SuggestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verified']);
    }

    public function suggest(Request $request)
    {
        $this->validation($request);

        $titles = $this->getTitles($request);

        $suggested_titles = $this->getSuggestedTitles($titles);

        $suggested_titles->each(function (Title $title) {
            UpdateTitle::dispatch($title);
        });

        $request
            ->user()
            ->suggestions()
            ->create([
                'titles'          => $titles,
                'recommendations' => $this->getSuggestionCollection($titles, $suggested_titles),
                'type'            => $request->exists('title') ? 'title' : 'user_list',
                'version'         => Suggestion::VERSION
            ]);

        $request->user()->notify(new TitlesSuggestions());

        return back()->with('success', 'We will notify you the suggestions!');
    }

    private function validation(Request $request)
    {
        $request->validate([
            'user_list'              => ['required_without_all:title'],
            'title'                  => ['required_without_all:user_list'],
            'remove_existing_titles' => ['required_if:user_list,true'],
        ]);
    }

    private function getTitles(Request $request): Collection
    {
        $titles = collect();
        if ($request->has('title')) {
            $titles->add(Title::find($request->get('title')));
        }
        if ($request->has('user_list')) {
            $userList = UserList::find($request->get('user_list'));
            $titles   = $userList->titles;
        }
        return $titles;
    }

    private function getSuggestedTitles(Collection $titles): Collection
    {
        $suggested_titles = collect();
        $titles->each(function (Title $title) use (&$suggested_titles) {
            $suggested_titles = $suggested_titles->merge($title->recommendations);
        });
        $suggested_titles = $suggested_titles->unique('imdb_id')->values();

        return $suggested_titles;
    }

    private function getSuggestionCollection(Collection $titles, Collection $suggested_titles): Collection
    {
        $suggestions = collect();

        $titles->each(function (Title $title) use ($suggested_titles, &$suggestions) {
            $suggestionItem = collect();

            $suggestionItem->put('title', sprintf('Because of "%s"', $title->title));
            $suggestionItem->put('items', $title->recommendations->intersect($suggested_titles));

            $suggestions->add($suggestionItem);
        });

        return $suggestions;
    }
}
