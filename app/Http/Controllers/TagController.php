<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Response;
use App\Models\Project;
use App\Models\Tag;
use App\Resources\TagCollection;
use App\Resources\TagResource;

/**
 * REST-API контроллер для взаимодействия с источниками обращений.
 */
class TagController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(Tag::class);
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
            'tags' => TagCollection::makeArray(
                Tag::byProject($project->id)->withWords()->get()
            )
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTagRequest  $request
     * @param  Project             $project
     * @return \App\Http\Response
     */
    public function store(StoreTagRequest $request, Project $project)
    {
        $tag = new Tag;
        $tag->forceFill($request->onlyFilled()->validated());
        $tag->project_id = $project->id;
        $tag->save();

        return Response::success()
            ->message('Новый тег обращения был успешно добавлен!')
            ->data(['tag' => TagResource::makeArray(
                Tag::whereId($tag->id)->withWords()->firstOrFail()
            )]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Tag   $tag
     * @return \App\Http\Response
     */
    public function show(Project $project, Tag $tag)
    {
        return Response::success()->data([
            'tag' => TagResource::makeArray(
                Tag::whereId($tag->id)->withWords()->firstOrFail()
            )
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Tag  $tag
     * @return \App\Http\Response
     */
    public function update(UpdateTagRequest $request, Project $project, Tag $tag)
    {
        $tag->forceFill($request->onlyFilled()->validated());
        $tag->save();

        return Response::success()
            ->message('Тег обращения был успешно обновлен!')
            ->data(['tag' => TagResource::makeArray(
                Tag::whereId($tag->id)->withWords()->firstOrFail()
            )]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Tag  $tag
     * @return \App\Http\Response
     */
    public function destroy(Project $project, Tag $tag)
    {
        $tag->delete();
        return Response::success()->message('Тег обращения удален');
    }
}
