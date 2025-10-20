<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Student;


class BiuController extends Controller
{
    public function test(){
        return view('test1');
    }
    public function users(){
        $users = DB::table('users')->get();
        return view('users.users_list',compact('users'));
    }
    public function form_add_student(){
        return view('users.form_addStudent');
    }

    public function student_list(){
        $students = DB::table('students')->get();
        return view('users.student_list', compact('students'));
    }
    public function save_stud(){
        $student=new student();
        $student->student_name=request('student_name');
        $student->student_email=request('student_email');
        $student->student_birthday=request('student_birthday');
        $student->save();
        return redirect()->back();
        
    }
}
