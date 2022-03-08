<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\V1\ImageManipulationController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('album', AlbumController::class);

Route::group([
    'as' => 'image.',
    'prefix' => 'v1/image',
    'controller' => ImageManipulationController::class
], function () {
    Route::get('', 'index');
    Route::get('by-album/{album}', 'byAlbum');
    Route::get('{image}', 'show');
    Route::post('resize', 'resize');
    Route::delete('{image}', 'destroy');
});
