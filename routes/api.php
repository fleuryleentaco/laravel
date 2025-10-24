<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('incoming-documents', [\App\Http\Controllers\Api\IncomingDocumentController::class, 'store']);
Route::get('incoming-documents/{id}/errors', [\App\Http\Controllers\Api\IncomingDocumentController::class, 'errors']);
Route::post('incoming-documents/{id}/send-errors', [\App\Http\Controllers\Api\IncomingDocumentController::class, 'sendErrors']);

// External systems may report errors found when analyzing documents
Route::patch('v1/documents/{id}/erreurs', [\App\Http\Controllers\Api\ExternalErrorController::class, 'report']);
