<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index(){
        $students = Student::all();
        return response()->json($students, 200);
    }

    public function store(StudentRequest $request){
        $student = Student::create([
            "name" => $request->name,
            "email" =>$request->email
        ]);
        if($student){

            return response()->json([
                'message' => 'Student created successfully',
                'student' => $student
            ], 201);        
        }
        return response()->json([
            'message' => 'feiled',
        ], 500);
    }

     public function update(StudentRequest $request, $id){
        $student = Student::findOrFail($id);
        $student->update([
            "name" => $request->name,
            "email" =>$request->email
        ]);
        if($student){
            return response()->json([
                'message' => 'Student updated successfully',
                'student' => $student
            ], 201);        
        }
        return response()->json([
            'message' => 'feiled',
        ], 500);
    }

    public function delete($id) {
         $student = Student::findOrFail($id);
         if($student){
            $student->delete();
            return response()->json([
                'message' => 'Student deleted successfully',
            ], 200);
         }
         return response()->json([
            'message' => 'Student not found',
        ], 404);
    }
}
