<?php

namespace App\Http\Controllers;

use App\Http\Requests\Claim\StoreClaimRequest;
use App\Http\Requests\Claim\UpdateClaimRequest;
use App\Http\Response;
use App\Models\Project;
use App\Models\Claim;
use App\Resources\ClaimCollection;
use App\Resources\ClaimResource;

/**
 * REST-API контроллер для взаимодействия с источниками обращений.
 */
class ClaimController extends Controller
{
    /**
     * Инициализация контроллера.
     */
    public function __construct()
    {
        $this->authorizeResource(Claim::class);
    }

    /**
     * Display a listing of the reclaim.
     * 
     * @param Project $project
     *
     * @return \App\Http\Response
     */
    public function index(Project $project)
    {
        return Response::success()->data([
            'claims' => ClaimCollection::makeArray(
                Claim::byProject($project->id)
                    ->with(['source', 'tags'])
                    ->orderBy('datetime', 'desc')
                    ->get()
            )
        ]);
    }

    /**
     * Store a newly created reclaim in storage.
     *
     * @param  StoreClaimRequest  $request
     * @param  Project             $project
     * @return \App\Http\Response
     */
    public function store(StoreClaimRequest $request, Project $project)
    {
        $claim = new Claim;
        $claim->fill($request->onlyFilled()->input());
        $claim->project_id = $project->id;
        $claim->save();

        if ($request->filled('tags')) {
            $claim->tags()->sync($request->input('tags', []));
        }

        $claim->load(['source', 'tags']);

        return Response::success()
            ->message('Новое обращение было успешно добавлено!')
            ->data(['claim' => ClaimResource::makeArray($claim)]);
    }

    /**
     * Display the specified reclaim.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Claim   $claim
     * @return \App\Http\Response
     */
    public function show(Project $project, Claim $claim)
    {
        $claim->load(['source', 'tags']);

        return Response::success()->data([
            'claim' => ClaimResource::makeArray($claim)
        ]);
    }

    /**
     * Update the specified reclaim in storage.
     *
     * @param  UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Claim  $claim
     * @return \App\Http\Response
     */
    public function update(UpdateClaimRequest $request, Project $project, Claim $claim)
    {
        $claim->fill($request->onlyFilled()->input());
        $claim->save();

        $claim->tags()->sync($request->input('tags', []));

        $claim->load(['tags', 'source']);

        return Response::success()
            ->message('Обращение было успешно обновлено!')
            ->data(['claim' => ClaimResource::makeArray($claim)]);
    }

    /**
     * Remove the specified reclaim from storage.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Claim  $claim
     * @return \App\Http\Response
     */
    public function destroy(Project $project, Claim $claim)
    {
        $claim->delete();
        return Response::success()->message('Обращение удалено.');
    }
}
