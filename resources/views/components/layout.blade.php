<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinTrack Pro - Financial Management System</title>
</head>
<body>
    {{-- If we wanna show stm only for guests not all people  --}}
    @guest
         {{-- <x-navlink href="/" :active=true >
    Home 
    </x-navlink> --}}
    @endguest
    <nav>
        <!-- class = {{  request()->is('home') ? 'bg-blue-900' : 'bg-blue-100' }} -->
    <x-navlink href="/" :active=true >
    Home 
    </x-navlink>
     <x-navlink href="/about" :active=true>
    About
    </x-navlink>
     <x-navlink href="/jobs" :active=true>
    jobs
    </x-navlink>
    </nav>
    <h1>
        {{$heading}}
    </h1>
    <p>

        {{$slot}}
    </p>
</body>
</html>