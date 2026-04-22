<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index(){
            // $jobs = Job::all();
    // dd($jobs[0]);
    // dd($jobs[0]->salary);
    // dd($jobs[0]->title);
        // dd('Hello');
        return view('home',[
        'greeting'=>'hello'
    ]);
    }
    public function create(){
         return view('jobs.create');
    }
    public function show(Job $job){
          // dd($id); // dump die 
    //     $job = Job::find($id);
    //     // dd($job);
    return view('jobs.show', ['job' => $job] );
    }
    public function store(){
        request()->validate([
        'title' => ['required','min:3'],
        'salary' => ['required']
    ]);
    Job::create([
        'title'=>request('title'),
        'salary'=>request('salary'),
        'employee_id'=>1,
    ]);
    // dd(request('title'));
    return redirect("/jobs");
    }
    public function update(Job $job){
          
    request()->validate([
        'title' => ['required','min:3'],
        'salary' => ['required']
    ]);
    // update the job
    //   $job = Job::findOrFail($id);
    $job->update([
        'title'=>request('title'),
        'salary'=>request('salary'),
    ]);
    // redirect - > jobs 
    return redirect('/jobs/',$job->id);
    }
    public function edit(Job $job){
             //  $job = Job::find($id);
    return view('jobs.edit', ['job' => $job] );
    }
    public function destroy(Job $job){
          // $job = Job::findOrFail($id);
    $job->delete();
    return view('/jobs');
    }
}
