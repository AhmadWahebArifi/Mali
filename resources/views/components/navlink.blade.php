@props(['active' => false])

<nav>
        <a {{$attributes}} >
            <h1>{{$slot}}</h1>
        </a>
</nav>