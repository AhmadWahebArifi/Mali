<x-layout>
        <x-slot:heading>
    jobs Page 
</x-slot:heading>
    <h1>Hello From jobs ! </h1>
    @foreach ($jobs as $job)
        <li>
        <a href="/jobs/{{$job['id'] }}">

            {{ $job['title']}} : Pays {{$job['salary']}} per year 
        </a>
        </li>
    @endforeach
    <div>
        {{ $jobs->links() }} if u use pagination
    </div>
</x-layout>