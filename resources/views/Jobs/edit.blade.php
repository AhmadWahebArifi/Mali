<x-layout>
        <x-slot:heading>
    edit Page : {{ $job->title }}
</x-slot:heading>
    <h1>Edit Job </h1>
    <p>TODO</p>
    <form method="POST" action="/jobs/{{ $job->id }}" >
        @csrf
        {{-- It will add a hidden input for CSRF for session and Cookie expiration vs  --}}
        @method('PATCH')
        Name
    <input id="title" name="title" type="text" value="{{ $job->title }}">
      @error('title')
         <p> {{ $message }} </p>
        @enderror
    </input >
    Password
    <input name="salary" id="salary" type="password" value="{{ $job->salary }}">
    </input>
         @error('salary')
         <p> {{ $message }} </p>
        @enderror
    Update
    <input type="submit">
</input form='delete-form'>
    Delete
    <input type="submit">
</input>

<a href="/jobs/{{ $job->id }}">
    Cancel
</a>
{{-- <div style="color:red">
    

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif
</div>    --}}
      
    </form>
    <form method="POST" action="/jobs/{{  $job->id }}" id="delete-form" style="display:none">
        @csrf
        @method('DELETE')
    </form>
</x-layout>