<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ShareController;
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
    Route::get('/upload-subFolder-exist',[UploadController::class,'uploadExist']);
    Route::post('/subFolder-upload',[UploadController::class,'SubFolderUpload']);
    Route::post('/upload-delete',[UploadController::class,'delete']);
    Route::post('/upload-option-check',[UploadController::class,'uploadOption']);
    Route::post('/delete-subFolder',[UploadController::class,'deleteSubFolder']);
    Route::get('/upload-option-both',[UploadController::class,'uploadOptionBoth']);
    Route::get('/upload-zip',[UploadController::class,'uploadZip']);
    Route::get('/upload-subFolder-zip',[UploadController::class,'uploadSubFolderZip']);
    Route::get('/share-upload-zip',[ShareController::class,'shareUploadZip']);
    Route::post('/upload-file-delete',[UploadController::class,'deleteFile']);
    Route::post('/upload-subFile-delete',[UploadController::class,'subFileDelete']);
    Route::get('/download-file',[UploadController::class,'download']);
    Route::get('/share-download-file',[ShareController::class,'shareDownload']);
    Route::get('/download-subFile',[UploadController::class,'downloadSubFile']);
    Route::get('/upload-list',[UploadController::class,'uploadList'])->name('upload-list');
    Route::get('/upload-list/folders/{id}',[UploadController::class,'getFolder']);
    Route::get('/upload-list/folders/sub-folders/{id}',[UploadController::class,'getSubFolder']);
    Route::get('/upload-list/data',[UploadController::class,'uploadData']);
    Route::get('/upload-list/type',[UploadController::class,'getType']);
    Route::get('/share-list/type',[ShareController::class,'getType']);
    Route::post('/category/save', [CategoryController::class, 'save'])->name('category.save');
    Route::post('/reset',[UploadController::class,'test']);
    Route::get('/getUser',[UploadController::class,'getUser']);
    Route::get('/myShare-getUser',[ShareController::class,'getUser']);
    Route::post('/store-share-user',[ShareController::class,'storeShare']);
    Route::get('/my-share',[ShareController::class,'myShare'])->name('my-share');
    Route::get('/my-share-list/data',[ShareController::class,'uploadData']);
    Route::post('/my-shareFile-delete',[ShareController::class,'deleteFile']);
    Route::get('/other-share',[ShareController::class,'otherShare'])->name('other-share');
    Route::get('/other-share-list/data',[ShareController::class,'otherShareData']);
    Route::get('/other-share-list/type',[ShareController::class,'getOtherShare']);
    Route::get('/otherShare-getUser',[ShareController::class,'getOtherUser']);
    Route::get('/delete-confirm',[UploadController::class,'deleteConfirm']);
    Route::post('/subfolder-upload',[UploadController::class,'FileUpload']);
    Route::post('/upload-subFolder-delete',[UploadController::class,'deleteUploadFolder']);
});




