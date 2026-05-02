    <h2>
        {{$job['title']}}
    </h2>
        <p>
        This job pays about {{$job['salary']}} per year ! 
        </p>

        <p>
            <a href='/jobs/{{ $job->id }}/edit'>
                Edit job
            </a> 
        </p>
