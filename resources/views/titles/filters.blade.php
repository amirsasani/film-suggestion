<form action="{{$action}}" class="form-inline d-flex justify-content-around">

    <div class="form-group mb-2">
        <label for="search" class="sr-only">Search</label>
        <input type="text"
               class="form-control"
               name="search"
               id="search"
               value="{{isset($selected['search'])?$selected['search']:'' }}"
               placeholder="Search ...">
    </div>

    <div class="form-group mb-2">
        <label for="start_year" class="sr-only">Start year</label>
        <select name="start_year" id="start_year" class="form-control">
            <option value="{{null}}">Select start year ...</option>
            @foreach($start_years as $start_year)
                <option
                    {{isset($selected['start_year']) && $selected['start_year'] == $start_year?'selected':'' }}
                    value="{{$start_year}}">
                    {{$start_year}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-2">
        <label for="end_year" class="sr-only">End year</label>
        <select name="end_year" id="end_year" class="form-control">
            <option value="{{null}}">Select end year ...</option>
            @foreach($end_years as $end_year)
                <option
                    {{isset($selected['end_year']) && $selected['end_year'] == $end_year?'selected':'' }}
                    value="{{$end_year}}">
                    {{$end_year}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-2">
        <label for="type" class="sr-only">Type</label>
        <select name="type" id="type" class="form-control">
            <option value="{{null}}">Select type ...</option>
            <option {{isset($selected['type']) && $selected['type'] == 'series'?'type':'' }}
                    value="series">
                Series
            </option>
            <option {{isset($selected['type']) && $selected['type'] == 'movie'?'type':'' }}
                    value="movie">
                Movie
            </option>
        </select>
    </div>

    <div class="form-group mb-2">
        <label for="rate" class="sr-only">Rate</label>
        <select name="rate" id="rate" class="form-control">
            <option value="{{null}}">Select Rate ...</option>
            @for($rate = 1; $rate <= 10; $rate++)
                <option
                    {{isset($selected['rate']) && $selected['rate'] == $rate?'selected':'' }}
                    value="{{$rate}}">
                    {{$rate}}
                </option>
            @endfor
        </select>
    </div>

    <div class="form-group mb-2">
        <label for="genres" class="sr-only">Genres</label>
        <select name="genres" id="genres" class="form-control">
            <option value="{{null}}">Select Genres ...</option>
            @foreach($genres as $genre_id => $genre_title)
                <option
                    {{isset($selected['genre']) && $selected['genre'] == $genre_id?'selected':'' }}
                    value="{{$genre_id}}">
                    {{$genre_title}}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary mb-2">Filter</button>

</form>
