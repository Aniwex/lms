<?php

namespace App\Http\Controllers;

use App\Http\Requests\Integration\StoreIntegrationRequest;
use App\Http\Requests\Integration\UpdateIntegrationRequest;
use App\Http\Response;
use App\Models\Integration;
use App\Resources\IntegrationCollection;
use App\Resources\IntegrationResource;

/**
 * REST-API контроллер для взаимодействия с интеграциями.
 */
class IntegrationController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(Integration::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Response
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
     * @param  \App\Http\Requests\Integration\StoreIntegrationRequest  $request
     * @return \App\Http\Response
     */
    public function store(StoreIntegrationRequest $request)
    {
        $integration = Integration::create($request->validated());

        return Response::success()
            ->message('Новая интеграция была добавлена успешно!')
            ->data(['integration' => IntegrationResource::makeArray($integration)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Integration  $integration
     * @return \App\Http\Response
     */
    public function show(Integration $integration)
    {
        return Response::success()->data([
            'integration' => IntegrationResource::makeArray($integration)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Integration\UpdateIntegrationRequest  $request
     * @param  \App\Models\Integration  $integration
     * @return \App\Http\Response
     */
    public function update(UpdateIntegrationRequest $request, Integration $integration)
    {
        $integration->fill($request->onlyFilled()->all());
        $integration->save();

        return Response::success()
            ->message('Интеграция была успешно обновлена!')
            ->data(['integration' => IntegrationResource::makeArray($integration)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Integration  $integration
     * @return \App\Http\Response
     */
    public function destroy(Integration $integration)
    {
        $integration->delete();
        return Response::success()->message('Интеграция удалена');
    }
}
