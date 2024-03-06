<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ToornamentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/hello', function(){
    return response()->json(['message' => 'Hello World']);
});

Route::get('/toornament/matches', [ToornamentController::class, 'getMatches']);
Route::get('toornament/groups', [ToornamentController::class, 'getGroups']);
Route::get('/toornament/rank', [ToornamentController::class, 'getRank']);
Route::get('toornament/sp3/s2/division', [ToornamentController::class, 'getUniqueDivision']);
Route::get('toornament/sp3/s2/matches/', [ToornamentController::class, 'getAllMatchFromDivision']);
