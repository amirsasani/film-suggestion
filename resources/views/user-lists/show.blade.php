@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 my-2">

                @include('partials.errors')

                <div class="card">
                    <div class="card-header">{{$list->title}}</div>
                    <div class="card-body">
                        <p>{{$list->description}}</p>
                        <hr>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <colgroup>
                                        <col span="1" style="width: 15%;">
                                        <col span="1" style="width: 30%;">
                                        <col span="1" style="width: 10%;">
                                        <col span="1" style="width: 10%;">
                                        <col span="1" style="width: 10%;">
                                        <col span="1" style="width: 5%;">
                                        <col span="1" style="width: 20%;">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">Genres</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Rate</th>
                                        <th scope="col">Actins</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse($list->titles as $title)
                                    <tr>
                                        <th scope="row">
                                            <img class="w-50" src="{{$title->thumb}}" alt="{{$title->title}}">
                                        </th>
                                        <td>{{$title->title}}</td>
                                        <td>{{$title->getYear()}}</td>
                                        <td>
                                            @foreach($title->genres as $genre)
                                                <span class="badge badge-success">{{$genre->title}}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="text-capitalize">{{$title->type}}</span>
                                        </td>
                                        <td>{{$title->rate}}</td>
                                        <td>
                                            <form class="form-inline" action="{{route('user-list.titles.remove', [$list, $title])}}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove from list</button>
                                            </form>
                                        </td>
                                    </tr>

                                    @empty
                                        <tr>
                                            <th scope="row" colspan="7" class="text-center">
                                                Your list is empty
                                            </th>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-4 my-2">
                <div class="card">
                    <div class="card-header">Add titles to <b>"<i>{{$list->title}}</i>"</b></div>
                    <div class="card-body p-0">
                        <ul class="list-group">
                            @foreach($titles_to_add as $title)
                                <form action="{{route('user-list.titles.add', [$list, $title])}}" method="post">
                                    @csrf
                                    <li class="list-group-item">
                                        <a href="{{$title->imdbLink()}}" class="w-75 align-self-center">
                                            <img class="card-img-top" src="{{$title->thumb}}" alt="{{$title->title}}">
                                        </a>
                                        <ul>
                                            <li>
                                                <span>Title:</span>
                                                <span>{{$title->title}}</span>
                                            </li>
                                            <li>
                                                <span>Genre:</span>
                                                <span>
                                                    @foreach($title->genres as $genre)
                                                        <span class="badge badge-dark">{{$genre->title}}</span>
                                                    @endforeach
                                                </span>
                                            </li>
                                            <li><span>Year</span> : <span>{{$title->getYear()}}</span></li>
                                            <li><span>Rate</span> : <span><b>{{$title->rate}}</b></span></li>
                                            <li>
                                                <span>Type</span> :
                                                <span class="text-capitalize">
                                                    {{$title->type}}
                                                </span>
                                            </li>
                                        </ul>
                                        <input type="submit" value="Add" class="btn btn-outline-success w-100 mt-3">
                                    </li>
                                </form>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
