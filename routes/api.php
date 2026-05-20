<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ClasificacionController;
use App\Http\Controllers\Api\JugadorController;
use App\Http\Controllers\Api\NoticiaController;
use App\Http\Controllers\Api\PartidoController;
use App\Http\Controllers\Api\PatrocinadorController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authenticated user (Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

// Catalog
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{slug}', [ProductoController::class, 'show']);
Route::get('/categorias', [CategoriaController::class, 'index']);

// News
Route::get('/noticias', [NoticiaController::class, 'index'])->name('api.noticias.index');
Route::get('/noticias/{slug}', [NoticiaController::class, 'show'])->name('api.noticias.show');

// Sports
Route::get('/jugadores', [JugadorController::class, 'index']);
Route::get('/partidos', [PartidoController::class, 'index']);
Route::get('/clasificacion', [ClasificacionController::class, 'index']);

// Brand
Route::get('/patrocinadores', [PatrocinadorController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);

// Orders (guest checkout or authenticated)
Route::post('/pedidos', [PedidoController::class, 'store']);
Route::middleware('auth:sanctum')->get('/pedidos/{codigo}', [PedidoController::class, 'show']);
