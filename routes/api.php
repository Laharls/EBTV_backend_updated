<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToornamentController;
use App\Http\Controllers\FullwipeController;

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

Route::get('/toornament/matches', [ToornamentController::class, 'getMatches']);
Route::get('toornament/groups', [ToornamentController::class, 'getGroups']);
Route::get('/toornament/rank', [ToornamentController::class, 'getRank']);
Route::get('toornament/sp3/s2/division', [ToornamentController::class, 'getUniqueDivision']);
Route::get('toornament/sp3/s2/matches/', [ToornamentController::class, 'getAllMatchFromDivision']);

Route::get('/fullwipe/teamMatch', [FullwipeController::class, 'getTeamMatch']);
Route::get('/fullwipe/groups', [FullwipeController::class, 'getFullwipeGroups']);
Route::get('/fullwipe/rank', [FullwipeController::class, 'getRank']);
Route::get('/fullwipe/groupName', [FullwipeController::class, 'getGroupName']);
Route::get('/fullwipe/roundName', [FullwipeController::class, 'getRoundName']);
