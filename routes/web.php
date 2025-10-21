<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiuController;

Route::get('/', function () {
    // Redirect to the app documents index (auth middleware will send guests to login)
    return redirect()->route('documents.index');
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

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('documents', [DocumentController::class,'index'])->name('documents.index');
    Route::get('documents/create', [DocumentController::class,'create'])->name('documents.create');
    Route::post('documents', [DocumentController::class,'store'])->name('documents.store');
    Route::get('documents/errors', [DocumentController::class,'errors'])->name('documents.errors');

    Route::get('reports/create', [DocumentController::class,'reportCreate'])->name('reports.create');
    Route::post('reports', [DocumentController::class,'reportStore'])->name('reports.store');

    // Profile routes for authenticated users
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('errors', [AdminController::class,'errors'])->name('errors');
    Route::post('errors/{id}/send', [AdminController::class,'sendMessage'])->name('sendMessage');
    Route::get('errors/{id}/re-analyze', [AdminController::class,'reAnalyze'])->name('reAnalyze');
    Route::get('approve/{id}', [AdminController::class,'approveDocument'])->name('approve');
    Route::get('reports', [AdminController::class,'reports'])->name('reports');
    Route::get('compare/{id}', [AdminController::class,'compare'])->name('compare');
    // users management
    Route::get('users', [AdminController::class,'users'])->name('users');
    Route::post('users/{id}/toggle-role', [AdminController::class,'toggleRole'])->name('users.toggleRole');
    Route::delete('users/{id}', [AdminController::class,'deleteUser'])->name('users.delete');
});
