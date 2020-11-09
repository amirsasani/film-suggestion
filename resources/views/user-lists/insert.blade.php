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
                        <form method="post" action="{{route('user-lists.insert')}}">
                            @csrf
                            <div class="form-group">
                                <label for="title">List title</label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       value="{{old('title')}}"
                                       placeholder="Enter list title">
                            </div>

                            <div class="form-group">
                                <label for="description">List description</label>
                                <textarea type="text"
                                          class="form-control"
                                          id="description"
                                          name="description"
                                          placeholder="Enter list description"
                                          rows="3">{{old('description')}}</textarea>
                            </div>


                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
