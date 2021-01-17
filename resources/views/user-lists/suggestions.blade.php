@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <p>listing <b>{{$titles->count()}}</b> of <b>{{$titles->total()}}</b> suggested titles</p>
            <div class="col-md-12 my-2">

                @include('partials.errors')

            </div>


            @forelse($titles as $title)

                <div class="card my-3 mx-1" style="width: 49% !important;">
                    <div class="row no-gutters">
                        <div class="col-auto">
                            <img src="{{$title->thumb}}" class="img-fluid" alt="">
                        </div>
                        <div class="col">
                            <div class="card-block p-3">
                                <h4 class="card-title">{{$title->title}}</h4>

                                <p>
                                    @foreach($title->genres as $genre)
                                        <span class="badge badge-dark">{{$genre->title}}</span>
                                    @endforeach
                                </p>

                                <ul class="pl-0" style="list-style: inside;">
                                    <li><span>Year</span> : <span>{{$title->getYear()}}</span></li>
                                    <li><span>Rate</span> : <span><b>{{$title->rate}}</b></span></li>
                                    <li><span>Type</span> : <span class="text-capitalize">{{$title->type}}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0">
                        <form class="form-inline p-0" action="{{route('user-list.titles.add', [$list, $title])}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 p-3">
                                Add to {{$list->title}} list
                            </button>
                        </form>
                    </div>
                </div>

            @empty
                <div class="col-md-12">
                    <h3 class="text-center text-info">No suggestions available :(</h3>
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
