@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                @include('user.profile-links')
            </div>

            <div class="col-md-10">

                @include('partials.errors')

                <div class="card">
                    <div class="card-header">Suggestion requests</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">based on</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($suggestions as $suggestion)
                                    <tr>
                                        <th scope="row">{{ $loop->index+1 }}</th>
                                        <td>{{$suggestion->type}}</td>
                                        <td>{{$suggestion->created_at->format('Y-M-d H:i:s')}}</td>
                                        <td>
                                            <a href="{{route('user.suggestion', $suggestion)}}" class="btn btn-block btn-light">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
