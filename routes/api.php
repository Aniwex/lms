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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/get','GetController');
});
// Data api
Route::get('data', 'DataController@getAllData');
Route::get('data/{id}', 'DataController@getData');
Route::post('data', 'DataController@createData');
Route::put('data/{id}', 'DataController@updateData');
Route::delete('data/{id}','DataController@deleteData');
// Integration api
Route::get('integrations', 'IntegrationController@getAllIntegrations');
Route::post('integrations', 'IntegrationController@createIntegrations');
Route::get('integrations/{id}', 'IntegrationController@getIntegrations');
Route::put('integrations/{id}', 'IntegrationController@updateIntegrations');
Route::delete('integrations/{id}','IntegrationController@deleteIntegrations');
// Sources api
Route::get('sources', 'SourceController@getAllSources');
Route::post('sources', 'SourceController@createSources');
Route::get('sources/{id}', 'SourceController@getSources');
Route::put('sources/{id}', 'SourceController@updateSources');
Route::delete('sources/{id}','SourceController@deleteSources');
// Tags api
Route::get('tags', 'TagsController@getAllTags');
Route::post('tags', 'TagsController@createTags');
Route::get('tags/{id}', 'TagsController@getTags');
Route::put('tags/{id}', 'TagsController@updateTags');
Route::delete('tags/{id}','TagsController@deleteTags');