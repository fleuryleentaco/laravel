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
    // user download and compare routes
    Route::get('documents/{id}/download', [DocumentController::class,'download'])->name('documents.download');
    Route::get('documents/{id}/compare', [DocumentController::class,'compare'])->name('documents.compare');
    // analyze a single document (user-triggered)
    Route::post('documents/{id}/analyze', [DocumentController::class,'analyze'])->name('documents.analyze');
    // add edit/update routes
    Route::get('documents/{id}/edit', [DocumentController::class,'edit'])->name('documents.edit');
    Route::put('documents/{id}', [DocumentController::class,'update'])->name('documents.update');
    Route::get('documents/{id}', [DocumentController::class,'show'])->name('documents.show');
    Route::delete('documents/{id}', [DocumentController::class,'destroy'])->name('documents.destroy');

    Route::get('reports/create', [DocumentController::class,'reportCreate'])->name('reports.create');
    Route::post('reports', [DocumentController::class,'reportStore'])->name('reports.store');

    // Admin: list all documents (admins only)
    Route::get('admin/documents', [\App\Http\Controllers\AdminController::class,'documents'])->name('admin.documents');

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
    // download a document via controller (secured)
    Route::get('documents/{id}/download', [AdminController::class,'download'])->name('documents.download');
    Route::get('reports', [AdminController::class,'reports'])->name('reports');
    Route::get('compare/{id}', [AdminController::class,'compare'])->name('compare');
    // users management
    Route::get('users', [AdminController::class,'users'])->name('users');
    Route::post('users/{id}/toggle-role', [AdminController::class,'toggleRole'])->name('users.toggleRole');
    Route::delete('users/{id}', [AdminController::class,'deleteUser'])->name('users.delete');
    Route::post('reports/{id}/send-result', [AdminController::class, 'sendReportResult'])->name('reports.sendResult');
});

Route::post('notifications/{id}/read', function($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return back();
})->name('notifications.read');
