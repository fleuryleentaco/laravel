<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\BiuController;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/test', function (){
//     return view('test1');
// });

Route::get('/test', [BiuController::class, 'test']);
Route::get('/users_list', [BiuController::class, 'users']);
Route::get('/form_add', [BiuController::class, 'form_add_student']);
Route::post('/save_student', [BiuController::class, 'save_stud']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
