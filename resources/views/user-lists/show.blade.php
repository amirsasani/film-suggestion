@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 my-2">

                @include('partials.errors')

                <div class="card">
                    <div class="card-header">{{$list->title}}</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <p>{{$list->description}}</p>
                            <a href="{{route('user-lists.suggest', $list)}}" class="btn btn-success">Suggest me titles</a>
                        </div>
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
                                                <span class="badge badge-secondary">{{$genre->title}}</span>
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

        </div>
    </div>
@endsection
