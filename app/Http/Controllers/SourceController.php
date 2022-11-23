<?php

namespace App\Http\Controllers;

use App\Http\Requests\Source\StoreSourceRequest;
use App\Http\Requests\Source\UpdateSourceRequest;
use App\Http\Response;
use App\Models\Project;
use App\Models\Source;
use App\Resources\SourceCollection;
use App\Resources\SourceResource;

/**
 * REST-API контроллер для взаимодействия с источниками обращений.
 */
class SourceController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(Source::class);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Project $project
     *
     * @return \App\Http\Response
     */
    public function index(Project $project)
    {
        return Response::success()->data([
            'sources' => SourceCollection::makeArray(
                Source::byProject($project->id)->with(['integration'])->get()
            )
        ]);
    }

    /**
     * Список полей для источника, в зависимости от интеграции.
     * 
     * @throws \App\Exceptions\BaseAppException
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Source   $source
     * @return \App\Http\Response
     */
    public function fields(Project $project, Source $source)
    {
        return Response::success()->data([
            'fields' => $source->getIntegrationClass()->fields()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSourceRequest  $request
     * @param  Project             $project
     * @return \App\Http\Response
     */
    public function store(StoreSourceRequest $request, Project $project)
    {
        $source = new Source;
        $source->fill($request->onlyFilled()->input());
        $source->project_id = $project->id;
        $source->save();

        $source->load(['integration']);

        return Response::success()
            ->message('Новый источник обращения был успешно добавлен!')
            ->data(['source' => SourceResource::makeArray($source)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Source   $source
     * @return \App\Http\Response
     */
    public function show(Project $project, Source $source)
    {
        $source->load(['integration']);

        return Response::success()->data([
            'source' => SourceResource::makeArray($source)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Source  $source
     * @return \App\Http\Response
     */
    public function update(UpdateSourceRequest $request, Project $project, Source $source)
    {
        $source->fill($request->onlyFilled()->input());
        $source->save();

        $source->load(['integration']);

        return Response::success()
            ->message('Источник обращения был успешно обновлен!')
            ->data(['source' => SourceResource::makeArray($source)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Source  $source
     * @return \App\Http\Response
     */
    public function destroy(Project $project, Source $source)
    {
        if ($source->claims()->exists()) {
            return Response::error(422)->message('Невозможно удалить источник, так как он закреплен за одним или несколькими обращениями (заявками).');
        }
        $source->delete();
        return Response::success()->message('Источник обращения удален');
    }
}
