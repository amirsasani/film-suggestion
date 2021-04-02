@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 my-2">

                @include('partials.errors')

                <div class="card">
                    <div class="card-header">
                        Add new title
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{route('titles.insert')}}">
                            @csrf
                            <div class="form-group">
                                <label for="imdb_id">IMDB ID</label>
                                <input type="text" class="form-control" id="imdb_id" name="imdb_id" placeholder="Enter IMDB ID">
                                <small id="emailHelp" class="form-text text-muted">
                                    https://www.imdb.com/title/tt<span class="text-danger">0108778</span>/
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
