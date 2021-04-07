<nav class="nav nav-pills flex-column">
    @foreach($links as $link)
        <a class="nav-item nav-link {{$link['active']?'active':''}}"
           href="{{$link['route']}}">
            {{$link['title']}}
        </a>
    @endforeach
</nav>
