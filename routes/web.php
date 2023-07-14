<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/upload',[UploadController::class,'index'])->name('upload');
    Route::post('/upload',[UploadController::class,'store']);
    Route::get('/upload-exist',[UploadController::class,'upload']);
    Route::post('/upload-delete',[UploadController::class,'delete']);
    Route::get('/upload-option-check',[UploadController::class,'uploadOption']);
    Route::get('//upload-option-both',[UploadController::class,'uploadOptionBoth']);
    Route::get('/upload-list',[UploadController::class,'uploadList'])->name('upload-list');
    Route::get('/upload-list/folders/{id}',[UploadController::class,'getFolder']);
    Route::get('/upload-list/folders/sub-folders/{id}',[UploadController::class,'getSubFolder']);
    Route::get('/upload-list/data',[UploadController::class,'uploadData']);
    Route::get('/upload-list/type',[UploadController::class,'getType']);
    Route::post('/category/save', [CategoryController::class, 'save'])->name('category.save');
    Route::post('/reset',[UploadController::class,'test']);
});




