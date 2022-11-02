<?php

use App\Integrations\Zadarma\Http\Controllers\ApiController as ZadarmaApiController;
use App\Integrations\Marquiz\Http\Controllers\ApiController as MarquizApiController;
use App\Integrations\Forms\Http\Controllers\ApiController as FormsApiController;
use App\Integrations\Roistat\Http\Controllers\ApiController as RoistatApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ================================
// Роуты для интеграции с задармой.
// =================================
Route::middleware(['zadarma.external'])->group(function () {

    Route::post('/zadarma/notify', [ZadarmaApiController::class, 'handleNotify']);

    Route::get('/zadarma/notify', function (Request $request) {
        if ($request->has('zd_echo'))
            exit($request->input('zd_echo'));
    });
});

// ================================
// Роуты для интеграции с марквизом.
// =================================
Route::post('/marquiz/hook', [MarquizApiController::class, 'handleHook']);

// =============================================
// Роуты для интеграции с формами обратной связи.
// =============================================
Route::post('/forms/claim', [FormsApiController::class, 'handleFormRequest']);

// =============================================
// Роуты для интеграции с Roistat(ловец лидов).
// =============================================
Route::post('/roistat/hook/{roistat_id}', [RoistatApiController::class, 'handleHook']);
