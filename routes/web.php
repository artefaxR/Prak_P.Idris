<?php

use App\Http\Controllers\AlamatController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// create route for user controller dengan middleware istoko
Route::resource('user', 'App\Http\Controllers\UserController');
Route::resource('produk', ProdukController::class);

Route::post(
    '/produk/destroy_image/{id}',
    [ProdukController::class, 'destroy_image']
)->name('produk.destroy-image');

Route::resource('province', ProvinceController::class);
Route::post(
    '/province/sync_province',
    [ProvinceController::class, 'sync_province']
)->name('province.sync-province');

Route::resource('city', CityController::class);
Route::post(
    '/city/sync_city',
    [CityController::class, 'sync_city']
)->name('city.sync-city');

Route::resource('alamat', AlamatController::class);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/dologin', [AuthController::class, 'dologin'])->name('dologin');
