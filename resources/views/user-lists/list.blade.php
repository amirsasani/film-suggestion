@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <p>listing <b>{{$lists->count()}}</b> of <b>{{$lists->total()}}</b> lists</p>
            <div class="col-md-12 my-2">

                @include('partials.errors')

            </div>

            @forelse($lists as $list)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{route('user-lists.show', $list)}}">{{$list->title}}</a>
                        </div>
                        <div class="card-body">
                            {{$list->description}}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <h3 class="text-center text-info">No Lists</h3>
                </div>
            @endforelse
        </div>
        <div class="row">
            <div class="col-sm-12">
                    <div class="pagination-wrapper">
                        {{ $lists->links() }}
                    </div>
            </div>
        </div>
    </div>
@endsection
