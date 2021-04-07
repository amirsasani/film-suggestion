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
                    <div class="card-header">Hello</div>
                    <div class="card-body">
                        Welcome to your profile
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
