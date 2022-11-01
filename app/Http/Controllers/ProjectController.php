<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Response;
use App\Models\Project;
use App\Resources\ProjectCollection;
use App\Resources\ProjectResource;

/**
 * REST-API контроллер для взаимодействия с проектами.
 */
class ProjectController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(Project::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Response
     */
    public function index()
    {
        return Response::success()->data([
            'projects' => ProjectCollection::makeArray(
                Project::get()
            )]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \App\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        return Response::success()
            ->message('Новый проект был успешно добавлен!')
            ->data(['project' => ProjectResource::makeArray($project)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \App\Http\Response
     */
    public function show(Project $project)
    {
        $project->load('users');

        return Response::success()->data([
            'project' => ProjectResource::makeArray($project)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Project\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \App\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->fill($request->onlyFilled()->except('users'));

        if ($request->filled('users')) {
            $project->users()->sync($request->input('users', []));
        }

        $project->save();

        $project->load('users');

        return Response::success()
            ->message('Проект был успешно обновлен!')
            ->data(['project' => ProjectResource::makeArray($project)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \App\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return Response::success()->message('Проект удален');
    }
}
