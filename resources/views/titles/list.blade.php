@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <p>listing <b>{{$titles->count()}}</b> of <b>{{$titles->total()}}</b> titles</p>
            <div class="col-md-12 my-2">

                @include('partials.errors')

                @include('titles.filters', ['action'=>route('titles.index')])
            </div>

            @forelse($titles as $title)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <a href="{{$title->imdbLink()}}" class="w-75 align-self-center">
                            <img class="card-img-top" src="{{$title->thumb}}" alt="{{$title->title}}">
                        </a>
                        <div class="card-header">
                            <a href="{{$title->imdbLink()}}">{{$title->title}}</a>
                        </div>
                        <div class="card-body pt-2">
                            <p>
                                @foreach($title->genres as $genre)
                                    <span class="badge badge-dark">{{$genre->title}}</span>
                                @endforeach
                            </p>

                            <ul>
                                <li><span>Year</span> : <span>{{$title->getYear()}}</span></li>
                                <li><span>Rate</span> : <span><b>{{$title->rate}}</b></span></li>
                                <li><span>Type</span> : <span class="text-capitalize">{{$title->type}}</span></li>
                            </ul>

                            @auth
                                <hr>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle w-100" type="button"
                                            id="addToListDropdownButton"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        Add to your list
                                    </button>
                                    <div class="dropdown-menu w-100  p-0" aria-labelledby="addToListDropdownButton">
                                        @foreach($user_lists as $list)
                                            <form class="form-inline dropdown-item p-0" action="{{route('user-list.titles.add', [$list, $title])}}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-link w-100">
                                                    {{$list->title}}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <h3 class="text-center text-info">No titles</h3>
                </div>
            @endforelse
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="pagination-wrapper">
                    {{ $titles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
