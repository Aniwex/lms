<?php

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

Route::middleware('auth:sanctum')->group(function() {

    // текущий авторизованный пользователь
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /**
     * Роуты для REST-api взаимодействия.
     */
    Route::apiResources([
        'integrations' => 'IntegrationController',
        'users'        => 'UserController',
        'projects'     => 'ProjectController'
    ]);

    // список ролей
    Route::get('roles', 'UserController@roles');

    /**
     * Проекто-зависимые REST-api роуты.
     */
    Route::prefix('projects/{project}')->group(function() {
        Route::apiResources([
            'sources' => 'SourceController'
        ]);
    });
});

