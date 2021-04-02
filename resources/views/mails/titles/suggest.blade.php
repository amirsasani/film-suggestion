@component('mail::message')
# Hello

You requested us to suggest you some movies/series.

@component('mail::table')
| Title             | Rate             | Type             | Year                  |
| :---------------- |:-----------------| :----------------| :--------------------:|
@foreach($titles as $title)
| {{$title->title}} | {{$title->rate}} | {{$title->type}} | {{$title->getYear()}} |
@endforeach
@endcomponent

Thank you for using our application! <br>
{{ config('app.name') }}
@endcomponent
