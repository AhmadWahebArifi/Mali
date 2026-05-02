<x-layout>
        <x-slot:heading>
    create Page 
</x-slot:heading>
    <h1>Create Job </h1>
    <p>TODO</p>
    <form method="POST" action="/jobs" >
        @csrf
        {{-- It will add a hidden input for CSRF for session and Cookie expiration vs  --}}

        <x-form-label for="name">
            Name
        </x-form-label>

    <x-form-input id="title" name="title" type="text">
      {{-- @error('title')
         <p> {{ $message }} </p>
        @enderror --}}
    </x-form-input >
        <x-form-error name='title'>

        </x-form-error>
    Salary
    <input name="salary" id="salary" type="password">
    </input>
         @error('salary')
         <p> {{ $message }} </p>
        @enderror
        <x-form-label>
            Button
        </x-form-label>
  <x-form-button>

  </x-form-button>
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
</x-layout>