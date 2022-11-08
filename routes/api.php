<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Response;
use App\Resources\UserResource;
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
        $user = $request->user();
        $user->load(['role', 'projects']);
        return Response::success()->data(UserResource::makeArray($user));
    });

    /**
     * Роуты для REST-api взаимодействия.
     */
    Route::apiResources([
        'integrations' => IntegrationController::class,
        'users'        => UserController::class,
        'projects'     => ProjectController::class
    ]);

    /**
     * Проекто-зависимые REST-api роуты.
     */
    Route::prefix('projects/{project}')->group(function() {
        Route::apiResources([
            'sources' => SourceController::class,
            'tags'    => TagController::class,
            'claims'  => ClaimController::class
        ]);

        // список полей для источника, в зависимости от интеграции
        Route::get('sources/{source}/integration-fields', [SourceController::class, 'fields']);
    });

    // список ролей
    Route::get('roles', [UserController::class, 'roles']);
});

