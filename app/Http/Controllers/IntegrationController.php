<?php

namespace App\Http\Controllers;

use App\Http\Requests\Integration\StoreIntegrationRequest;
use App\Http\Requests\Integration\UpdateIntegrationRequest;
use App\Http\Response;
use App\Models\Integration;
use App\Resources\IntegrationCollection;

/**
 * REST-API контроллер для взаимодействия с интеграциями.
 */
class IntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::success()->data([
            'integrations' => IntegrationCollection::makeArray(
                Integration::get()
            )]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreIntegrationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIntegrationRequest $request)
    {
        return Response::success()->message('Integration create request mock');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function show(Integration $integration)
    {
        return Response::success()->message('Integration show request mock');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateIntegrationRequest  $request
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIntegrationRequest $request, Integration $integration)
    {
        return Response::success()->message('Integration update request mock');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Integration  $integration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Integration $integration)
    {
        return Response::success()->message('Integration delete request mock');
    }
}
