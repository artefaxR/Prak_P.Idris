<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransaksiController;

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

Route::resource('user', UserController::class);
Route::middleware('auth')->resource('produk', ProdukController::class);
Route::middleware('auth')->resource('province', ProvinceController::class);
Route::middleware('auth')->resource('city', CityController::class);
Route::middleware('auth')->resource('alamat', AlamatController::class);

Route::middleware(['auth'])
    ->get('/home', [HomeController::class, 'index'])
    ->name('home.index');

Route::middleware(['auth', 'KONSUMEN'])->get(
    '/transaksi/daftar_produk',
    [TransaksiController::class, 'daftar_produk']
)->name('transaksi.daftar-produk');

Route::middleware(['auth', 'KONSUMEN'])->post(
    '/transaksi/get-ongkir',
    [TransaksiController::class, 'get_ongkir']
)->name('transaksi.get-ongkir');

Route::middleware(['auth', 'KONSUMEN'])->post(
    '/transaksi/checkout',
    [TransaksiController::class, 'checkout']
)->name('transaksi.checkout');

Route::middleware(['auth', 'KONSUMEN'])->get(
    '/transaksi/keranjang',
    [TransaksiController::class, 'keranjang']
)->name('transaksi.keranjang');

Route::middleware(['auth', 'KONSUMEN'])->delete(
    '/transaksi/hapus-keranjang/{transaksi}',
    [TransaksiController::class, 'hapus_keranjang']
)->name('transaksi.hapus-keranjang');

Route::middleware(['auth', 'KONSUMEN'])->get(
    '/transaksi/bayar',
    [TransaksiController::class, 'bayar']
)->name('transaksi.bayar');

Route::middleware(['auth', 'KONSUMEN'])->get(
    '/transaksi/produk/{id}',
    [TransaksiController::class, 'produk']
);

Route::post(
    '/produk/destroy_image/{id}',
    [ProdukController::class, 'destroy_image']
)->name('produk.destroy-image');

Route::post(
    '/province/sync_province',
    [ProvinceController::class, 'sync_province']
)->name('province.sync-province');

Route::post(
    '/city/sync_city',
    [CityController::class, 'sync_city']
)->name('city.sync-city');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/dologin', [AuthController::class, 'dologin'])->name('dologin');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
