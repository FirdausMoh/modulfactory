<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });



// Route::get('home', [HomeController::class, 'index'])->name('home');



Auth::routes();
Route:: redirect('/', '/login');
Route::post('/login', [LoginController::class, 'authenticate']);//mesaage

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('profile', ProfileController::class,'__invoke')->name('profile');
    Route::resource('employees', EmployeeController::class);
    //download file
    Route::get('download-file/{employeeId}', [EmployeeController::class,
    'downloadFile'])->name('employees.downloadFile');
});



//meletakkan file di local
Route::get('/local-disk', function() {
    Storage::disk('local')->put('local-example.txt', 'This is local example content');
    return asset('storage/local-example.txt');
});
//meletakkan file di publick disk
Route::get('/public-disk', function() {
    Storage::disk('public')->put('public-example.txt', 'This is public example content');
    return asset('storage/public-example.txt');
});
//menmapilkan isi file local
Route::get('/retrieve-local-file', function() {
    if (Storage::disk('local')->exists('local-example.txt')) {
        $contents = Storage::disk('local')->get('local-example.txt');
    }
    else {
        $contents = 'File does not exist';
    }

    return $contents;
});
//menamplkan isi file public
Route::get('/retrieve-public-file', function() {
    if (Storage::disk('public')->exists('public-example.txt')) {
        $contents = Storage::disk('public')->get('public-example.txt');
    }
    else {
        $contents = 'File does not exist';
    }

    return $contents;
});
//route download file local
Route::get('/download-local-file', function() {
    return Storage::download('local-example.txt', 'local file');//('nama file','nama file download')
});
//route download file public
Route::get('/download-public-file', function() {
    return Storage::download('public/public-example.txt', 'public file');//('nama file','nama file download')
});

//Menampilkan URL, Path dan Size dari File
Route::get('/file-url', function() {
    // Just prepend "/storage" to the given path and return a relative URL
    $url = Storage::url('local-example.txt');
    return $url;
});

Route::get('/file-size', function() {
    $size = Storage::size('local-example.txt');
    return $size;
});

Route::get('/file-path', function() {
    $path = Storage::path('local-example.txt');
    return $path;
});
//route menmapilkan view upload
Route::get('/upload-example', function() {
    return view('upload_example');
});
// route menyimpan file via form
Route::post('/upload-example', function(Request $request) {
    $path = $request->file('avatar')->store('public');
    return $path;
})->name('upload-example');
//delete file pada storage(local)
Route::get('/delete-local-file', function(Request $request) {
    Storage::disk('local')->delete('local-example.txt');
    return 'Deleted';
});
//delete file pada storage(public)
Route::get('/delete-public-file', function(Request $request) {
    Storage::disk('public')->delete('djH4ujWEc7PPjMLIp0UOw0xP1aEMilLlsQaC9VGt.pdf');
    return 'Deleted';
});

